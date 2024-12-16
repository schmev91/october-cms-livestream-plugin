<?php namespace Pi\Livestream\Controllers;

use Backend\Classes\Controller;
use Illuminate\Contracts\Support\MessageBag;
use Pi\Livestream\Models\LiveStreamUser;
use Pi\Livestream\Services\AlabapayLivestreamService;
use Session;

class Livestreams extends Controller
{
    public function __construct(private AlabapayLivestreamService $alabapayLivestream)
    {
        $this->alabapayLivestream = new AlabapayLivestreamService();
    }

    public function sendUnprocessAble(MessageBag $fieldErrs)
    {
        return response()->json([
            "code"   => "invalid_data",
            "fields" => $fieldErrs,
         ], 422);
    }

    /**
     * Lấy danh sách livestream đạng hoạt động
     */
    public function activeLivestreamsGet()
    {
        $data      = input();
        $validator = \Validator::make($data, [
            'pageSize' => 'nullable|integer',
         ]);
        if ($validator->fails()) {
            return $this->sendUnprocessAble($validator->messages());
        }

        $pageSize = $data[ "pageSize" ] ?? 10;

        $result = LiveStreamUser::with("user")
            ->where("status", "active")
            ->paginate($pageSize);

        return response()->json($result);
    }

    /**
     * Khởi tạo token livestream
     *
     * @return Illuminate\Http\JsonResponse
     */
    public function tokenGenerate()
    {
        $data      = input();
        $validator = \Validator::make($data, [
            "channelName"       => "required|string",
            "collected_diamond" => "nullable|integer",
         ]);
        if ($validator->fails()) {
            return $this->sendUnprocessAble($validator->messages());
        }

        $user   = optional(Session::get('user'));
        $userId = $user->id ?? null;

        $isLivestreamExists = LiveStreamUser::where("user_id", $userId)
            ->where("status", "active")
            ->first();

        // Trả lỗi nếu livestream đã tồn tại
        if ($isLivestreamExists) {
            return response()->json([
                'code'    => 'already_active_livestream',
                'message' => 'You already have an active livestream.',
             ], 400);
        }

        try {
            // Tạo token livestream
            $agoraToken = $this->alabapayLivestream->generateToken(
                $data[ "channelName" ]
            );
        } catch (\Pi\Livestream\Exceptions\InvalidAccessKeyException $err) {
            return $err->render();
        }

        // Trả lỗi nếu tạo token không thành công
        if (!$agoraToken) {
            return response()->json([
                'code'    => 'invalid_livestream',
                'message' => 'Failed to generate token.',
             ], 400);
        }

        try {
            // Tạo record LiveStreamUser
            LiveStreamUser::create([
                "agora_token"       => $agoraToken,
                "collected_diamond" => $data[ 'collected_diamond' ] ?? 0,
                "fullname"          => $user->fullname ?? "Anonymous",
                "host_identity"     => $userId ?? "Host",
                "joined_users"      => [  ],
                "user_id"           => $userId,
             ]);
        } catch (\Throwable $th) {
            return response()->json([
                'code'   => 'invalid_data',
                'fields' => $th->getMessage(),
             ], 400);

        }

        // Success - Phản hồi với token livestream
        return response()->json([
            'status'  => 200,
            'message' => "Successfully generated livestream token.",
            'token'   => $agoraToken,
         ]);

    }
    public function livestreamEnd()
    {
        $data      = input();
        $validator = \Validator::make($data, [
            "watching_count"    => "required|integer",
            "comment_count"     => "required|integer",
            "collected_diamond" => "required|integer",
         ]);
        if ($validator->fails()) {
            return $this->sendUnprocessAble($validator->messages());
        }

        $user   = optional(Session::get('user'));
        $userId = $user->id ?? null;

        try {
            $livestreamRecord = LivestreamUser::where("user_id", $userId)
                ->where("status", "active")
                ->firstOrFail();
        } catch (\Throwable $th) {
            return response()->json([
                'code'    => 'livestream_not_found',
                'message' => 'Livestream Not Found.',
             ], 404);
        }

        // Lấy agora token từ record
        $token = $livestreamRecord->agora_token;

        try {
            // Kiểm tra trạng thái kết thúc bản ghi của livestream này trên alabapay
            $isEndRemoteStreamSuccess = $this->alabapayLivestream->endLivestream(
                $token,
                ...$data
            );
        } catch (\Pi\Livestream\Exceptions\InvalidAccessKeyException $err) {
            return $err->render();
        }

        // Trả lỗi nếu kết thúc livestream trên Alabapay không thahf công
        if (!$isEndRemoteStreamSuccess) {
            return response()->json([
                'code'    => 'invalid_livestream',
                'message' => 'Failed to end the livestream.',
             ], 400);
        }

        // Cập nhật trạng thái và dữ liệu trong bản ghi của livestream
        $livestreamRecord->update([ "status" => "ended", ...$data ]);

        return response()->json([
            'status'  => 200,
            'message' => 'Livestream ended successfully.',
            'data'    => $livestreamRecord,
         ]);

    }
}
