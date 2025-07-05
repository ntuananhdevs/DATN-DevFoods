<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Tra cứu giao dịch</title>
    <!-- Bootstrap core CSS -->
    <link href="/vnpay_php/assets/bootstrap.min.css" rel="stylesheet" />
    <!-- Custom styles for this template -->
    <link href="/vnpay_php/assets/jumbotron-narrow.css" rel="stylesheet">
    <script src="/vnpay_php/assets/jquery-1.11.3.min.js"></script>
</head>

<body>
    <div class="container">
        <div class="header clearfix">
            <h3 class="text-muted">VNPAY DEMO</h3>
        </div>
        <div style="width: 100%;padding-top:0px;font-weight: bold;color: #333333">
            <h3>Querydr</h3>
        </div>
        <div style="width: 100% ;border-bottom: 2px solid black;padding-bottom: 20px">
            <form action="/vnpay_php/vnpay_querydr.php" id="frmCreateOrder" method="post">
                <div class="form-group">
                    <label>Mã GD thanh toán cần quy vấn (vnp_TxnRef):</label>
                    <input class="form-control" data-val="true" name="txnRef" type="text" value="" />
                </div>
                <div class="form-group">
                    <label>Thời gian khởi tạo GD thanh toán (vnp_TransactionDate):</label>
                    <input class="form-control" data-val="true" name="transactionDate" type="text" placeholder="yyyyMMddHHmmss" value="" />
                </div>
                <input type="submit" class="btn btn-default" value="Querydr" />
            </form>
        </div>

        <?php
        date_default_timezone_set('Asia/Ho_Chi_Minh');
        error_reporting(E_ALL & E_NOTICE & E_DEPRECATED);
        require_once("config.php");

        function callAPI_auth($method, $url, $data)
        {
            $curl = curl_init();
            switch ($method) {
                case "POST":
                    curl_setopt($curl, CURLOPT_POST, 1);
                    if ($data)
                        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                    break;
                case "PUT":
                    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
                    if ($data)
                        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                    break;
                default:
                    if ($data)
                        $url = sprintf("%s?%s", $url, http_build_query($data));
            }
            // OPTIONS:
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json'
            ));
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            // EXECUTE:
            $result = curl_exec($curl);
            if (!$result) {
                die("Connection Failure");
            }
            curl_close($curl);
            return $result;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $vnp_RequestId = rand(1,10000); // Mã truy vấn
            $vnp_Command = "querydr"; // Mã api
            $vnp_TxnRef = $_POST["txnRef"]; // Mã tham chiếu của giao dịch
            $vnp_OrderInfo = "Query transaction"; // Mô tả thông tin
            //$vnp_TransactionNo= ; // Tuỳ chọn, Mã giao dịch thanh toán của CTT VNPAY
            $vnp_TransactionDate = $_POST["transactionDate"]; // Thời gian ghi nhận giao dịch
            $vnp_CreateDate = date('YmdHis'); // Thời gian phát sinh request
            $vnp_IpAddr = $_SERVER['REMOTE_ADDR']; // Địa chỉ IP của máy chủ thực hiện gọi API


            $datarq = array(
                "vnp_RequestId" => $vnp_RequestId,
                "vnp_Version" => "2.1.0", 
                "vnp_Command" => $vnp_Command,
                "vnp_TmnCode" => $vnp_TmnCode,
                "vnp_TxnRef" => $vnp_TxnRef,
                "vnp_OrderInfo" => $vnp_OrderInfo,
                //$vnp_TransactionNo= ; 
                "vnp_TransactionDate" => $vnp_TransactionDate,
                "vnp_CreateDate" => $vnp_CreateDate,
                "vnp_IpAddr" => $vnp_IpAddr
            );

            $format = '%s|%s|%s|%s|%s|%s|%s|%s|%s';

            $dataHash = sprintf(
                $format,
                $datarq['vnp_RequestId'], //1
                $datarq['vnp_Version'], //2
                $datarq['vnp_Command'], //3
                $datarq['vnp_TmnCode'], //4
                $datarq['vnp_TxnRef'], //5
                $datarq['vnp_TransactionDate'], //6
                $datarq['vnp_CreateDate'], //7
                $datarq['vnp_IpAddr'], //8
                $datarq['vnp_OrderInfo']//9
            ); 

            $checksum = hash_hmac('SHA512', $dataHash, $vnp_HashSecret);
            $datarq["vnp_SecureHash"] = $checksum;
            $txnData = callAPI_auth("POST", $apiUrl, json_encode($datarq));
            $ispTxn = json_decode($txnData, true);

            echo "
            <div>
            <label>API Response:</label>
            <pre>
            $txnData
            </pre>
            </div>";
        }

        ?>
    </div>
</body>

</html>