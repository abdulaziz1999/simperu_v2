<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Duitku extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/userguide3/general/urls.html
	 */
	public function index()
	{
		$this->load->view('welcome_message');
	}

	function pay(){
		$merchantCode = 'D9174'; // dari duitku
		$merchantKey = '11fca2d38ac9a876a5ad337006aa8aa3'; // dari duitku

		$timestamp = round(microtime(true) * 1000); //in milisecond
		$paymentAmount = 40000;
		$merchantOrderId = time() . ''; // dari merchant, unique
		$productDetails = 'Test Pay with duitku';
		$email = 'azizmentor96@gmail.com'; // email pelanggan merchant
		$phoneNumber = '08123456789'; // nomor tlp pelanggan merchant (opsional)
		$additionalParam = ''; // opsional
		$merchantUserInfo = ''; // opsional
		$customerVaName = 'John Doe'; // menampilkan nama pelanggan pada tampilan konfirmasi bank
		$callbackUrl = 'https://abdulaziz.nurulfikri.com/simperu_v2/duitku/callback'; // url untuk callback
		$returnUrl = 'https://abdulaziz.nurulfikri.com/simperu_v2/duitku/cekpembayaran';//'http://example.com/return'; // url untuk redirect
		$expiryPeriod = 10; // untuk menentukan waktu kedaluarsa dalam menit
		$signature = hash('sha256', $merchantCode.$timestamp.$merchantKey);
		// $paymentMethod = '014'; //digunakan untuk direksional pembayaran

		// Detail pelanggan
		$firstName = "John";
		$lastName = "Doe";

		// Alamat
		$alamat = "Jl. Kembangan Raya";
		$city = "Jakarta";
		$postalCode = "11530";
		$countryCode = "ID";

		$address = array(
			'firstName' => $firstName,
			'lastName' => $lastName,
			'address' => $alamat,
			'city' => $city,
			'postalCode' => $postalCode,
			'phone' => $phoneNumber,
			'countryCode' => $countryCode
		);

		$customerDetail = array(
			'firstName' => $firstName,
			'lastName' => $lastName,
			'email' => $email,
			'phoneNumber' => $phoneNumber,
			'billingAddress' => $address,
			'shippingAddress' => $address
		);


		$item1 = [
			'name' => 'Test Item 1',
			'price' => 10000,
			'quantity' => 1];

		// $item2 = array(
		// 	'name' => 'Test Item 2',
		// 	'price' => 30000,
		// 	'quantity' => 3);

		$itemDetails = [$item1];

		$params = array(
			'paymentAmount' => $paymentAmount,
			'merchantOrderId' => $merchantOrderId,
			'productDetails' => $productDetails,
			'additionalParam' => $additionalParam,
			'merchantUserInfo' => $merchantUserInfo,
			'customerVaName' => $customerVaName,
			'email' => $email,
			'phoneNumber' => $phoneNumber,
			'itemDetails' => $itemDetails,
			'customerDetail' => $customerDetail,
			'callbackUrl' => $callbackUrl,
			'returnUrl' => $returnUrl,
			'expiryPeriod' => $expiryPeriod,
			// 'paymentMethod' => $paymentMethod
		);

		$params_string = json_encode($params);
		//echo $params_string;
		$url = 'https://api-sandbox.duitku.com/api/merchant/createinvoice'; // Sandbox
		// $url = 'https://api-prod.duitku.com/api/merchant/createinvoice'; // Production

		//log transaksi untuk debug 
		// file_put_contents('log_createInvoice.txt', "* log *\r\n", FILE_APPEND | LOCK_EX);
		// file_put_contents('log_createInvoice.txt', $params_string . "\r\n\r\n", FILE_APPEND | LOCK_EX);
		// file_put_contents('log_createInvoice.txt', 'x-duitku-signature:' . $signature . "\r\n\r\n", FILE_APPEND | LOCK_EX);
		// file_put_contents('log_createInvoice.txt', 'x-duitku-timestamp:' . $timestamp . "\r\n\r\n", FILE_APPEND | LOCK_EX);
		// file_put_contents('log_createInvoice.txt', 'x-duitku-merchantcode:' . $merchantCode . "\r\n\r\n", FILE_APPEND | LOCK_EX);
		$ch = curl_init();


		curl_setopt($ch, CURLOPT_URL, $url); 
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params_string);                                                                  
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
			'Content-Type: application/json',                                                                                
			'Content-Length: ' . strlen($params_string),
			'x-duitku-signature:' . $signature ,
			'x-duitku-timestamp:' . $timestamp ,
			'x-duitku-merchantcode:' . $merchantCode    
			)                                                                       
		);   
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

		//execute post
		$request = curl_exec($ch);
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		if($httpCode == 200)
		{
			$result = json_decode($request, true);
			header('location: '. $result['paymentUrl']);
			// print_r($result, false);
			echo "paymentUrl :". $result['paymentUrl'] . "<br />";
			echo "reference :". $result['reference'] . "<br />";
			echo "statusCode :". $result['statusCode'] . "<br />";
			echo "statusMessage :". $result['statusMessage'] . "<br />";
		}
		else
		{
			// echo $httpCode . " " . $request ;
			echo $request ;
		}
	}

	function pay2(){
		// $key = $this->key('psb');
        $merchantCode = 'D9174'; // dari duitku
        $merchantKey = '11fca2d38ac9a876a5ad337006aa8aa3'; // dari duitku
        $paymentAmount = '10000'; 
        $paymentMethod = '014'; // VC = Credit Card
        $merchantOrderId = time() . ''; // dari merchant, unik
        $productDetails = "PSB Aziz " ;
        $email = 'azizmentor96@gmail.com'; // email pelanggan anda
        $phoneNumber = '089669001989'; // nomor telepon pelanggan anda (opsional)
        $additionalParam = ''; // opsional
        $merchantUserInfo = "PSB DQM - A"; // opsional
        $customerVaName = "PSB DQM - Aziz"; // tampilan nama pada tampilan konfirmasi bank
        $callbackUrl = ''; // url untuk callback
        $returnUrl = 'https://abdulaziz.nurulfikri.com/simperu_v2'; // url untuk redirect
        $expiryPeriod = 5040; // atur waktu kadaluarsa dalam hitungan menit
        $signature = md5($merchantCode . $merchantOrderId . $paymentAmount . $merchantKey);

        // Customer Detail
        $firstName = 'AZIZ';
        $lastName = " Axiz";

        // Address
        $alamat = " ";
        $city = 'BOGOR';
        $postalCode = " ";
        $countryCode = " ";

        $address = array(
            'firstName' => $firstName,
            'lastName' => $lastName,
            'address' => $alamat,
            'city' => $city,
            'postalCode' => $postalCode,
            'phone' => $phoneNumber,
            'countryCode' => $countryCode
        );

        $customerDetail = array(
            'firstName' => $firstName,
            'lastName' => $lastName,
            'email' => $email,
            'phoneNumber' => $phoneNumber,
            'billingAddress' => $address,
            'shippingAddress' => $address
        );


        $item1 = array(
            'name' => "PSB DQM a.n  Aziz",
            'price' => $paymentAmount,
            'quantity' => 1);

        $itemDetails = array($item1);

        $params = array(
            'merchantCode' => $merchantCode,
            'paymentAmount' => $paymentAmount,
            'paymentMethod' => $paymentMethod,
            'merchantOrderId' => $merchantOrderId,
            'productDetails' => $productDetails,
            'additionalParam' => $additionalParam,
            'merchantUserInfo' => $merchantUserInfo,
            'customerVaName' => $customerVaName,
            'email' => $email,
            'phoneNumber' => $phoneNumber,
            'itemDetails' => $itemDetails,
            'customerDetail' => $customerDetail,
            'callbackUrl' => $callbackUrl,
            'returnUrl' => $returnUrl,
            'signature' => $signature,
            'expiryPeriod' => $expiryPeriod
        );

        $params_string = json_encode($params);
        //echo $params_string;
        $url = 'https://sandbox.duitku.com/webapi/api/merchant/v2/inquiry'; 
		// $url = 'https://sandbox.duitku.com/webapi/api/disbursement/inquirysandbox';
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url); 
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params_string);                                                                  
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
            'Content-Type: application/json',                                                                                
            'Content-Length: ' . strlen($params_string))                                                                       
        );   
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

        //execute post
        $request = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if($httpCode == 200){
            $result = json_decode($request, true);
            
            // $dataInsert = [
            //     // 'idcsantri' => $idcsantri,
            //     // 'idtahunajar' => $this->tahunajar()->id,
            //     'merchantOrderId' => $merchantOrderId,
            //     'paymentUrl' => $result['paymentUrl'],
            //     'merchantCode' => $result['merchantCode'],
            //     'reference' => $result['reference'],
            //     'vaNumber' => $result['vaNumber'],
            //     'amount' => $result['amount'],
            //     'statusCode' => $result['statusCode'],
            //     'statusMessage' => "WAITING",
            //     'paymentMethod' => $paymentMethod
            // ];
            // $this->db->insert('duitku', $dataInsert);
            // if($this->db->affected_rows() > 0){
                // $this->db->where([
                //     'idcsantri' => $idcsantri,
                //     'idtahunajar' => $this->tahunajar()->id
                // ])->update('mutasi_csantri', [
                //     'tagihan' => $result['amount'],
                //     'status_bayar' => 'Menunggu Pembayaran',
                //     'metode' => 'duitku',
                //     'randomword' => $result['paymentUrl']
                // ]);

                // $pesan	= "*PSB Darul Qur'an Mulia*\n\n";	
                // $pesan .= "Atas Nama 		: PPDB DQM - ".$csantri->nama."\n";
                // $pesan .= "Jumlah Bayar		: ".rupiah($result['amount'])."\n";
                // $pesan .= "Url Pembayaran	: \n".$result['paymentUrl']."\n";
                // $this->app_model->curlWa($csantri->nohandphone, $pesan);

				echo "paymentUrl :". $result['paymentUrl'] . "<br />";
				echo "merchantCode :". $result['merchantCode'] . "<br />";
				echo "reference :". $result['reference'] . "<br />";
				echo "vaNumber :". $result['vaNumber'] . "<br />";
				echo "amount :". $result['amount'] . "<br />";
				echo "statusCode :". $result['statusCode'] . "<br />";
				echo "statusMessage :". $result['statusMessage'] . "<br />";
                header('location: https://abdulaziz.nurulfikri.com/simperu_v2');
            // }else{
            //     $this->session->set_flashdata('error', "Silahkan Coba Lagi Untuk Pemilihan Metode Pembayaran");
            //     redirect($_SERVER['HTTP_REFERER'],'refresh');
            // }


		
            // $this->output->set_content_type('application/json')->set_output(json_encode($result));
            
        }else{
            echo $httpCode;
        }

	}


	function callback(){
		$apiKey = '11fca2d38ac9a876a5ad337006aa8aa3'; // API key anda
		$merchantCode = isset($_POST['merchantCode']) ? $_POST['merchantCode'] : null; 
		$amount = isset($_POST['amount']) ? $_POST['amount'] : null; 
		$merchantOrderId = isset($_POST['merchantOrderId']) ? $_POST['merchantOrderId'] : null; 
		$productDetail = isset($_POST['productDetail']) ? $_POST['productDetail'] : null; 
		$additionalParam = isset($_POST['additionalParam']) ? $_POST['additionalParam'] : null; 
		$paymentCode = isset($_POST['paymentCode']) ? $_POST['paymentCode'] : null; 
		$resultCode = isset($_POST['resultCode']) ? $_POST['resultCode'] : null; 
		$merchantUserId = isset($_POST['merchantUserId']) ? $_POST['merchantUserId'] : null; 
		$reference = isset($_POST['reference']) ? $_POST['reference'] : null; 
		$signature = isset($_POST['signature']) ? $_POST['signature'] : null; 

		//log callback untuk debug 
		// file_put_contents('callback.txt', "* Callback *\r\n", FILE_APPEND | LOCK_EX);

		if(!empty($merchantCode) && !empty($amount) && !empty($merchantOrderId) && !empty($signature))
		{
			$params = $merchantCode . $amount . $merchantOrderId . $apiKey;
			$calcSignature = md5($params);

			if($signature == $calcSignature)
			{
				//Callback tervalidasi
				//Silahkan rubah status transaksi anda disini
				// file_put_contents('callback.txt', "* Berhasil *\r\n\r\n", FILE_APPEND | LOCK_EX);

			}
			else
			{
				// file_put_contents('callback.txt', "* Bad Signature *\r\n\r\n", FILE_APPEND | LOCK_EX);
				throw new Exception('Bad Signature');
			}
		}
		else
		{
			// file_put_contents('callback.txt', "* Bad Parameter *\r\n\r\n", FILE_APPEND | LOCK_EX);
			throw new Exception('Bad Parameter');
		}

	}

	function cekpembayaran(){
		echo "cekpembayaran";
	}
}
