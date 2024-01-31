<?php

if (!function_exists('send_fcm_notification')) {
    function send_fcm_notification(string $deviceToken, $notification, array $data = []): bool|string
    {
        $accessToken = config('services.notification.token');
        $URL         = config('services.notification.base_url');

        $keys = '';
        foreach ($data['keys'] ?? [] as $key => $value) $keys .= sprintf('"%s": "%s",', $key, is_array($value) ? implode(',', $value) : $value);
        $keys = rtrim($keys, ",");
        if (!empty($keys)) $keys = ',' . $keys;

        $post_data = '{
           "notification":{
              "title":"' . ($notification->title ?? '') . '",
              "body":"' . $notification->message . '",
              "image":"",
              "sound":"default",
              "android_channel_id":"fcm_default_channel"
           },
           "priority":"high",
           "data":{
              "click_action":"FLUTTER_NOTIFICATION_CLICK",
              "notification_id":"' . $notification->id . '",
              "model_id":"' . (is_array($notification->model_id) ? implode(',', $notification->model_id) : $notification->model_id) . '",
              "key":"' . $notification->for . '"
              ' . $keys . '
           },
           "android":{
              "priority":"high",
              "notification":{
                 "title":"' . ($notification->title ?? '') . '",
                 "body":"' . $notification->message . '",
                 "sound":"default"
              }
           },
           "apns":{
              "aps":{
                 "alert":{
                    "title":"' . ($notification->title ?? '') . '",
                    "body":"' . $notification->message . '"
                 },
                 "badge":1
              },
              "headers":{
                 "apns-priority":10
              },
              "payload":{
                 "aps":{
                    "sound":"default"
                 }
              },
              "fcm_options":{
                 "image":""
              },
              "customKey":"customValue"
              ' . $keys . '
           },
           "time_to_live":3600,
           "to":"' . $deviceToken . '"
        }';

        $crl = curl_init();

        $headers   = [];
        $headers[] = 'Content-type: application/json';
        $headers[] = 'Authorization: key=' . $accessToken;
        curl_setopt($crl, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($crl, CURLOPT_URL, $URL);
        curl_setopt($crl, CURLOPT_HTTPHEADER, $headers);

        curl_setopt($crl, CURLOPT_POST, true);
        curl_setopt($crl, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($crl, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($crl);

        curl_close($crl);

        logs()->info('fcm:result::' . $notification->title, [
            'rest'         => json_decode($result),
            'notification' => [
                'id'        => $notification->id,
                'title'     => $notification->title,
                'message'   => $notification->message,
                'for'       => $notification->for,
                'model_id'  => is_array($notification->model_id) ? implode(',', $notification->model_id) : $notification->model_id,
                'keys'      => $keys,
                'post_data' => '
                    "data":{
                      "click_action":"FLUTTER_NOTIFICATION_CLICK",
                      "notification_id":"' . $notification->id . '",
                      "model_id":"' . (is_array($notification->model_id) ? implode(',', $notification->model_id) : $notification->model_id) . '",
                      "key":"' . $notification->for . '"
                      ' . $keys . '
                   }
                ',
            ],
            'device_token' => $deviceToken,
        ]);

        return $result;
    }
}

if (!function_exists('get_lat_lng_from_address')) {
    function get_lat_lng_from_address(string $address): ?array
    {
        $apiKey = config('services.google.map.api_key');

        try {
            $address = str_replace(" ", "+", $address);

            $json = file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?address={$address}&key={$apiKey}");
            $json = json_decode($json);

            if (isset($json->{'results'}) && count($json->{'results'}) > 0) {
                $lat  = $json->{'results'}[0]->{'geometry'}->{'location'}->{'lat'};
                $long = $json->{'results'}[0]->{'geometry'}->{'location'}->{'lng'};
                return ['lat' => $lat, 'lng' => $long];
            }

            return null;
        } catch (Exception $exception) {
            return null;
        }
    }
}

if (!function_exists('lat_long_dist_of_two_points')) {
    function lat_long_dist_of_two_points($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 3959): float|int
    {
        if (($latitudeFrom == $latitudeTo) && ($longitudeFrom == $longitudeTo)) {
            return 0;
        }

        /* -------------------- Method 1 -------------------- */
        //        $theta = $longitudeFrom - $longitudeTo;
        //        $dist  = sin(deg2rad($latitudeFrom)) * sin(deg2rad($latitudeTo)) + cos(deg2rad($latitudeFrom)) * cos(deg2rad($latitudeTo)) * cos(deg2rad($theta));
        //        $dist  = acos($dist);
        //        $dist  = rad2deg($dist);
        //        $miles = $dist * 60 * 1.1515;
        //        $unit  = strtoupper($unit);
        //
        //        if ($unit == "K") {
        //            return ($miles * 1.609344);
        //        } else if ($unit == "N") {
        //            return ($miles * 0.8684);
        //        } else {
        //            return $miles;
        //        }

        /* -------------------- Method 2 -------------------- */
        // 3959 = result in miles, 6371000 = result in meters
        $latFrom = deg2rad($latitudeFrom);
        $lonFrom = deg2rad($longitudeFrom);
        $latTo   = deg2rad($latitudeTo);
        $lonTo   = deg2rad($longitudeTo);

        $lonDelta = $lonTo - $lonFrom;
        // $a        = pow(cos($latTo) * sin($lonDelta), 2) + pow(cos($latFrom) * sin($latTo) - sin($latFrom) * cos($latTo) * cos($lonDelta), 2);
        $a        = ((cos($latTo) * sin($lonDelta)) ** 2) + ((cos($latFrom) * sin($latTo) - sin($latFrom) * cos($latTo) * cos($lonDelta)) ** 2);
        $b        = sin($latFrom) * sin($latTo) + cos($latFrom) * cos($latTo) * cos($lonDelta);

        $angle = atan2(sqrt($a), $b);
        return $angle * $earthRadius;

        /* -------------------- Method 3 -------------------- */
        //        $pi = pi();
        //        $x  = sin($latitudeFrom * $pi / 180) *
        //            sin($latitudeTo * $pi / 180) +
        //            cos($latitudeFrom * $pi / 180) *
        //            cos($latitudeTo * $pi / 180) *
        //            cos(($longitudeTo * $pi / 180) - ($longitudeFrom * $pi / 180));
        //        $x  = atan((sqrt(1 - pow($x, 2))) / $x);
        //        return abs((1.852 * 60.0 * (($x / $pi) * 180)) / 1.609344);
    }
}
