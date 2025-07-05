<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Hoàn tiền giao dịch</title>
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
            <h3>Refund</h3>
        </div>
        <div style="width: 100% ;border-bottom: 2px solid black;padding-bottom: 20px">
            <form action="/vnpay_php/vnpay_refund.php" id="frmCreateOrder" method="post">
                <div class="form-group">
                    <label>Mã GD thanh toán cần hoàn (vnp_TxnRef):</label>
                    <input class="form-control" data-val="true" name="TxnRef" type="text" value="" />
                </div>
                <div class="form-group">
                    <label>Kiểu hoàn tiền (vnp_TransactionType):</label>
                    <select name="TransactionType" id="trantype" class="form-control">
                        <option value="02">Hoàn tiền toàn phần</option>
                        <option value="03">Hoàn tiền một phần</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="amount">Số tiền hoàn:</label>
                    <input class="form-control" max="100000000" min="1" name="Amount" type="number" value="" />
                </div>
                <div class="form-group">
                    <label>Thời gian khởi tạo GD thanh toán (vnp_TransactionDate):</label>
                    <input class="form-control" data-val="true" name="TransactionDate" type="text" placeholder="yyyyMMddHHmmss" value="" />
                </div>
                <div class="form-group">
                    <label>User khởi tạo hoàn (vnp_CreateBy):</label>
                    <input class="form-control" data-val="true" name="CreateBy" type="text" value="" />
                </div>
                <input type="submit" class="btn btn-default" value="Refund" />
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
            $vnp_Command = "refund"; // Mã api
            $vnp_TransactionType = $_POST["TransactionType"]; // 02 hoàn trả toàn phần - 03 hoàn trả một phần
            $vnp_TxnRef = $_POST["TxnRef"]; // Mã tham chiếu của giao dịch
            $vnp_Amount = $_POST["Amount"] * 100; // Số tiền hoàn trả
            $vnp_OrderInfo = "Hoan Tien Giao Dich"; // Mô tả thông tin
            $vnp_TransactionNo = "0"; // Tuỳ chọn, "0": giả sử merchant không ghi nhận được mã GD do VNPAY phản hồi.
            $vnp_TransactionDate = $_POST["TransactionDate"]; // Thời gian ghi nhận giao dịch
            $vnp_CreateDate = date('YmdHis'); // Thời gian phát sinh request
            $vnp_CreateBy = $_POST["CreateBy"]; // Người khởi tạo hoàn tiền
            $vnp_IpAddr = $_SERVER['REMOTE_ADDR']; // Địa chỉ IP của máy chủ thực hiện gọi API

            $ispTxnRequest = array(
                "vnp_RequestId" => $vnp_RequestId,
                "vnp_Version" => "2.1.0",
                "vnp_Command" => $vnp_Command,
                "vnp_TmnCode" => $vnp_TmnCode,
                "vnp_TransactionType" => $vnp_TransactionType,
                "vnp_TxnRef" => $vnp_TxnRef,
                "vnp_Amount" => $vnp_Amount,
                "vnp_OrderInfo" => $vnp_OrderInfo,
                "vnp_TransactionNo" => $vnp_TransactionNo,
                "vnp_TransactionDate" => $vnp_TransactionDate,
                "vnp_CreateDate" => $vnp_CreateDate,
                "vnp_CreateBy" => $vnp_CreateBy,
                "vnp_IpAddr" => $vnp_IpAddr
            );

            $format = '%s|%s|%s|%s|%s|%s|%s|%s|%s|%s|%s|%s|%s';

            $dataHash = sprintf(
                $format,
                $ispTxnRequest['vnp_RequestId'], //1
                $ispTxnRequest['vnp_Version'], //2
                $ispTxnRequest['vnp_Command'], //3
                $ispTxnRequest['vnp_TmnCode'], //4
                $ispTxnRequest['vnp_TransactionType'], //5
                $ispTxnRequest['vnp_TxnRef'], //6
                $ispTxnRequest['vnp_Amount'], //7
                $ispTxnRequest['vnp_TransactionNo'],  //8
                $ispTxnRequest['vnp_TransactionDate'], //9
                $ispTxnRequest['vnp_CreateBy'], //10
                $ispTxnRequest['vnp_CreateDate'], //11
                $ispTxnRequest['vnp_IpAddr'], //12
                $ispTxnRequest['vnp_OrderInfo'] //13
            ); 

            $checksum = hash_hmac('SHA512', $dataHash, $vnp_HashSecret);
            $ispTxnRequest["vnp_SecureHash"] = $checksum;
            $txnData = callAPI_auth("POST", $apiUrl, json_encode($ispTxnRequest));
            $ispTxn = json_decode($txnData, true);

            echo "
            <div>
            <pre>
            <label>API Response:</label>
            <code class='language-html' data-lang='html'>$txnData</code>
            </pre>
            </div>";
        }

        ?>
    </div>
</body>

</html>