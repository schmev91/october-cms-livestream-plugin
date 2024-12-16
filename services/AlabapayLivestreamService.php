<?php

namespace Pi\Livestream\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Pi\Livestream\Models\LiveStreamUser;

class AlabapayLivestreamService
{
    private string $baseApiUrl = "";

    public function __construct()
    {
        $this->baseApiUrl =
            config("pi.livestream::config.alabapay_url") .
            "api/merchant/livestream/";
    }

    private function apiClient(): PendingRequest
    {
        $headers = [
            "Authorization" => config("pi.livestream::config.access_key"),
            "Content-Type"  => "application/json",
         ];
        return Http::withHeaders($headers);
    }

    /**
     * Lấy danh sách người dùng đang livestream
     *
     * @return void
     */
    public function getActiveLivestreams(int $pageSize)
    {
        return LiveStreamUser::with("user")
            ->where("status", "active")
            ->paginate($pageSize);
    }

    /**
     * Lấy token livestream từ alabapay
     *
     * @return string
     */
    public function generateToken(string $channelName)
    {
        $apiUrl      = $this->baseApiUrl . "generate-token";
        $requestBody = compact("channelName");

        try {
            // get the token from alabapay api
            $response = $this->apiClient()->post($apiUrl, $requestBody);
        } catch (\Throwable $th) {
            // if fall into this, maybe the alabapay server is down
            return false;
        }

        // check if the access_key seen as valid
        if ($response->unauthorized()) {
            throw new \Pi\Livestream\Exceptions\InvalidAccessKeyException();
        }

        // handle failed token generate request
        if (!$response->successful()) {
            return false;
        }

        $token = $response->json("token");

        // return the agora token
        return $token;
    }

    /**
     * Kết thúc livestream
     *
     * @return bool
     */
    public function endLivestream(
        string $agora_token,
        int $watching_count,
        int $comment_count,
        int $collected_diamond
    ): bool {
        $apiUrl = $this->baseApiUrl . "end";

        $requestBody = compact(
            "agora_token",
            "watching_count",
            "comment_count",
            "collected_diamond"
        );

        try {
            $response = $this->apiClient()->post($apiUrl, $requestBody);
        } catch (\Throwable $th) {
            return false;
        }

        if ($response->unauthorized()) {
            throw new \Pi\Livestream\Exceptions\InvalidAccessKeyException();
        }

        // return true if successful
        if ($response->successful()) {
            return true;
        }

        return false;
    }
}
