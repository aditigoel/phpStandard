<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\BankAccountRequest;
use App\Http\Traits\CommonTrait;
use App\Interfaces\PaymentInterface;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\User;
use App\Models\UserBankAccount;
use App\Models\UserCard;
use App\Models\UserPaymentInfo;
use App\Models\UserWallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use MangoPay;
use Response;

class PaymentController extends Controller implements PaymentInterface
{
    use CommonTrait;

    private $mangopay;

    public function __construct(\MangoPay\MangoPayApi $mangopay)
    {
        $this->mangopay = $mangopay;
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     *
     *
     *  @SWG\Get(
     *   path="/mangopay/createAccount",
     *   summary="create user account on mango pay",
     *   consumes={"multipart/form-data"},
     *   produces={"application/json"},
     *   tags={"Payments"},
     *   @SWG\Parameter(
     *     name="user_id",
     *     in="query",
     *     required=true,
     *     type="number",
     *     description = "User id",
     *   ),
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")
     * )
     *
     */

    public function createAccount(Request $request)
    {
        $requested_data = $request->all();
        $user = User::where('id', $requested_data['user_id'])->first();

        try {
            $dob = (int) $user['dob']; // random any date or if it is in your project then use user data of birth timestamp
            $UserNatural = new \MangoPay\UserNatural();
            $UserNatural->FirstName = $user['full_name'];
            $UserNatural->LastName = $user['full_name'];
            $UserNatural->Birthday = $dob;
            $UserNatural->Nationality = 'GB';
            $UserNatural->CountryOfResidence = 'GB';
            $UserNatural->Occupation = '';
            $UserNatural->IncomeRange = '';
            $UserNatural->ProofOfIdentity = '';
            $UserNatural->ProofOfAddress = '';
            $UserNatural->Capacity = '';
            $UserNatural->PersonType = 'NATURAL';
            $UserNatural->Email = $user['email'];
            $UserNatural->KYCLevel = 'Light';
            $UserNatural->Id = '';
            $UserNatural->Tag = '';
            $UserNatural->CreationDate = time();
            $Result = $this->mangopay->Users->Create($UserNatural);
            if ($Result) {
                $paymentModel = [];
                $paymentModel['user_id'] = $user['id'];
                $paymentModel['mangopay_user_id'] = $Result->Id;
                $paymentModel['created_at'] = time();
                $paymentModel['updated_at'] = time();
                UserPaymentInfo::create($paymentModel);

                $msg['status'] = 200;
                $msg['msg'] = "Mangopay user created successfully.";
                $msg['data'] = $Result->Id;
                return $msg;
            } else {
                $msg['status'] = 400;
                $msg['msg'] = "Sorry unable to create account!";
                return $msg;
            }
        } catch (MangoPay\Libraries\ResponseException $e) {
            $msg['status'] = 400;
            $msg['msg'] = $e->GetErrorDetails();
            return $msg;
            // handle/log the response exception with code $e->GetCode(), message $e->GetMessage() and error(s) $e->GetErrorDetails()
        } catch (MangoPay\Libraries\Exception $e) {
            $msg['status'] = 400;
            $msg['msg'] = $e->GetMessage();
            return $msg;
            // handle/log the exception $e->GetMessage()
        }
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     *
     *
     *  @SWG\Get(
     *   path="/mangopay/createWallet",
     *   summary="create user wallet on mango pay",
     *   consumes={"multipart/form-data"},
     *   produces={"application/json"},
     *   tags={"Payments"},
     *   @SWG\Parameter(
     *     name="user_id",
     *     in="query",
     *     required=true,
     *     type="number",
     *     description = "User id",
     *   ),
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")
     * )
     *
     */
    public function createWallet(Request $request)
    {
        $requested_data = $request->all();
        $user_mangopay_id = UserPaymentInfo::where('user_id', $requested_data['user_id'])->first();
        if ($user_mangopay_id) {

            $currency = \Config::get('variable.currency');
            try {
                $Wallet = new \MangoPay\Wallet();
                $Wallet->Owners = array($user_mangopay_id->mangopay_user_id);
                $Wallet->Description = "Wallet";
                $Wallet->Currency = $currency;
                $Result = $this->mangopay->Wallets->Create($Wallet);
                if ($Result) {
                    $wallet_id = $Result->Id;
                    $walletModel = [];
                    $walletModel['user_id'] = $requested_data['user_id'];
                    $walletModel['wallet_id'] = $Result->Id;
                    $walletModel['status'] = 1;
                    $walletModel['created_at'] = time();
                    $walletModel['updated_at'] = time();
                    UserWallet::create($walletModel);

                    $msg['status'] = 200;
                    $msg['msg'] = "Wallet created successfully.";
                    $msg['data'] = $Result->Id;
                    return $msg;
                } else {
                    $msg['status'] = 400;
                    $msg['msg'] = "Sorry unable to create wallet!";
                    return $msg;
                }
            } catch (MangoPay\Libraries\ResponseException $e) {
                $msg['status'] = 400;
                $msg['msg'] = $e->GetErrorDetails();
                return $msg;
                // handle/log the response exception with code
                // $e->GetCode(), message $e->GetMessage() and error(s) $e->GetErrorDetails()
            } catch (MangoPay\Libraries\Exception $e) {
                $msg['status'] = 400;
                $msg['msg'] = $e->GetMessage();
                return $msg;
                // handle/log the exception $e->GetMessage()
            }
        } else {
            $msg['status'] = 400;
            $msg['msg'] = "No mangopay id of user found.";
            return $msg;
        }
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     *
     *
     *  @SWG\Get(
     *   path="/mangopay/getUserWallet",
     *   summary="get user wallets",
     *   consumes={"multipart/form-data"},
     *   produces={"application/json"},
     *   tags={"Payments"},
     *   @SWG\Parameter(
     *     name="Authorization",
     *     in="header",
     *     required=false,
     *     description = "Enter Authorization Token",
     *     type="string"
     *   ),
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")
     * )
     *
     */
    public function getUserWallet(Request $request)
    {
        $requested_data = $request->all();
        $user_mangopay_id = UserPaymentInfo::where('user_id', $requested_data['user_id'])->first();
        if ($user_mangopay_id) {
            try {

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_URL, "https://api.sandbox.mangopay.com/v2.01/igniva44/users/" . $user_mangopay_id->mangopay_user_id . "/wallets/");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

                $headers = array();
                $headers[] = "Content-Type: application/json";
                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json',
                    'Authorization: Basic aWduaXZhNDQ6bm42dk1VMTFhcnQxS29MZ3dGR2hRQnhEbUU5bUswR2VmTHl4Z3NvMWVjNjM5UkNxSFY='));

                $result1 = curl_exec($ch);
                $result1 = json_decode($result1);
                if (curl_errno($ch)) {
                    // echo 'Error:' . curl_error($ch);
                    $data['status'] = 400;
                    $data['msg'] = curl_errno($ch);
                    return $data;
                } else {
                    $data['status'] = 200;
                    $data['msg'] = 'User wallets fetched successfully.';
                    $data['data'] = $result1;
                    return $data;
                }
            } catch (MangoPay\Libraries\ResponseException $e) {
                $data['status'] = 400;
                $data['msg'] = $e->GetErrorDetails();
                return $data;
                // handle/log the response exception with code $e->GetCode(), message $e->GetMessage() and error(s) $e->GetErrorDetails()
            } catch (MangoPay\Libraries\Exception $e) {
                $data['status'] = 400;
                $data['msg'] = $e->GetMessage();
                return $data;
                // handle/log the exception $e->GetMessage()
            }
        } else {
            $msg['status'] = 400;
            $msg['msg'] = "No mangopay id of user found.";
            return $msg;
        }
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     *
     *
     *  @SWG\Get(
     *   path="/mangopay/viewWallet",
     *   summary="get notifications of a user",
     *   consumes={"multipart/form-data"},
     *   produces={"application/json"},
     *   tags={"Payments"},
     *   @SWG\Parameter(
     *     name="Authorization",
     *     in="header",
     *     required=false,
     *     description = "Enter Authorization Token",
     *     type="string"
     *   ),
     *  @SWG\Parameter(
     *     name="wallet_id",
     *     in="query",
     *     required=true,
     *     description = "Enter Authorization Token",
     *     type="string"
     *   ),
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")
     * )
     *
     */
    public function viewWallet(Request $request)
    {
        $requested_data = $request->all();

        try {
            $WalletId = $requested_data['wallet_id'];
            $Result = $this->mangopay->Wallets->Get($WalletId);
            if ($Result) {
                $msg['status'] = 200;
                $msg['msg'] = "Wallet details fetched successfully.";
                $msg['wallet_details'] = $Result;
                return $msg;
            } else {
                $msg['status'] = 400;
                $msg['msg'] = "Sorry unable to fetch wallet details!";
                return $msg;
            }
        } catch (MangoPay\Libraries\ResponseException $e) {
            $msg['status'] = 400;
            $msg['msg'] = $e->GetErrorDetails();
            return $msg;
            // handle/log the response exception with code $e->GetCode(), message $e->GetMessage() and error(s) $e->GetErrorDetails()
        } catch (MangoPay\Libraries\Exception $e) {
            $msg['status'] = 400;
            $msg['msg'] = $e->GetMessage();
            return $msg;
            // handle/log the exception $e->GetMessage()
        }
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     *
     *
     *  @SWG\Post(
     *   path="/mangopay/addCard",
     *   summary="add user cards on mangopay",
     *   consumes={"multipart/form-data"},
     *   produces={"application/json"},
     *   tags={"Payments"},
     * @SWG\Parameter(
     *     name="Authorization",
     *     in="header",
     *     required=false,
     *     description = "Enter Authorization Token",
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="card_number",
     *     in="formData",
     *     required=true,
     *     type="number",
     *     description = "Card number",
     *   ),
     * @SWG\Parameter(
     *     name="expire_month",
     *     in="formData",
     *     required=true,
     *     type="number",
     *     description = "Expire month",
     *   ),
     * @SWG\Parameter(
     *     name="expire_year",
     *     in="formData",
     *     required=true,
     *     type="number",
     *     description = "Expire year",
     *   ),
     * @SWG\Parameter(
     *     name="cvv",
     *     in="formData",
     *     required=true,
     *     type="number",
     *     description = "Cvv",
     *   ),
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")
     * )
     *
     */
    public function addCard(Request $request)
    {
        $requested_data = $request->all();
        $user_mangopay_id = UserPaymentInfo::where('user_id', $requested_data['user_id'])->first();

        if ($user_mangopay_id) {
            try {
                // create card registration
                $currency = \Config::get('variable.currency');
                $CardRegistration = new \MangoPay\CardRegistration();
                $CardRegistration->Tag = "custom meta";
                $CardRegistration->UserId = $user_mangopay_id->mangopay_user_id;
                $CardRegistration->Currency = $currency;
                $CardRegistration->CardType = 'CB_VISA_MASTERCARD';
                $Result = $this->mangopay->CardRegistrations->Create($CardRegistration);
                if ($Result) {
                    $accessKey = $Result->AccessKey;
                    $PreregistrationData = $Result->PreregistrationData;

                    $createCard = $this->createCardId($requested_data, $Result);
                    if ($createCard['status'] == 200) {
                        $cardModel = [];
                        $cardModel['user_id'] = $requested_data['user_id'];
                        $cardModel['card_id'] = $createCard['data']->CardId;
                        $cardModel['registration_id'] = $createCard['registration_id'];
                        $cardModel['status'] = 1;
                        $cardModel['created_at'] = time();
                        $cardModel['updated_at'] = time();
                        UserCard::create($cardModel);

                        $msg['status'] = 200;
                        $msg['msg'] = 'Card added successfully.';
                        return $msg;
                    } else {
                        return $createCard;
                    }
                } else {
                    $msg['status'] = 400;
                    $msg['msg'] = 'Error in creating card registration.';
                }
            } catch (MangoPay\Libraries\ResponseException $e) {
                $msg['status'] = 400;
                $msg['msg'] = $e->GetErrorDetails();
                return $msg;
                // handle/log the response exception with code $e->GetCode(), message $e->GetMessage() and error(s) $e->GetErrorDetails()
            } catch (MangoPay\Libraries\Exception $e) {
                $msg['status'] = 400;
                $msg['msg'] = $e->GetMessage();
                return $msg;
                // handle/log the exception $e->GetMessage()
            }
        } else {
            $msg['status'] = 400;
            $msg['msg'] = "No mangopay id of user found.";
            return $msg;
        }
    }

    /*
     * create card id
     */
    private function createCardId($cardData, $registrationData)
    {
        // create card id
        $accessKey = $registrationData->AccessKey;
        $PreregistrationData = $registrationData->PreregistrationData;
        $card_number = $cardData['card_number'];
        $expirey_date = $cardData['expire_month'] . $cardData['expire_year'];
        $cvv = $cardData['cvv'];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_URL, "https://homologation-webpayment.payline.com/webpayment/getToken");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "accessKeyRef=$accessKey&data=$PreregistrationData&cardNumber=$card_number&cardExpirationDate=$expirey_date&cardCvx=$cvv");
        curl_setopt($ch, CURLOPT_POST, 1);

        $headers = array();
        $headers[] = "Content-Type: application/x-www-form-urlencoded";
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        $registeration_id = $registrationData->Id;
        if (curl_errno($ch)) {
            // echo 'Error:' . curl_error($ch);
            $msg['status'] = 400;
            $msg['msg'] = curl_error($ch);
            return $msg;
        } else {
            curl_close($ch);

            $update_card_details = $this->updateCardDetails($result, $registeration_id);
            if ($update_card_details) {
                $get_card_id = $this->getCardId($registeration_id);
                if ($get_card_id['status'] == 200) {
                    $get_card_id['registration_id'] = $registeration_id;
                    return $get_card_id;
                } else {
                    return $get_card_id;
                }
            } else {
                return $update_card_details;
            }
        }
    }

    /*
     * update card with details
     */
    private function updateCardDetails($result, $registeration_id)
    {
        $data = [];
        try {
            $CardRegistration = new \MangoPay\CardRegistration();
            $CardRegistration->Tag = "custom meta";
            $CardRegistration->Id = $registeration_id;
            $CardRegistration->RegistrationData = $result;

            $Result3 = $this->mangopay->CardRegistrations->Update($CardRegistration);
            $data['status'] = 200;
            $data['msg'] = 'success';
            $data['data'] = $Result3;
        } catch (MangoPay\Libraries\ResponseException $e) {
            $data['status'] = 400;
            $data['msg'] = $e->GetMessage();
            // handle/log the response exception with code $e->GetCode(), message $e->GetMessage() and error(s) $e->GetErrorDetails()
        } catch (MangoPay\Libraries\Exception $e) {
            $data['status'] = 400;
            $data['msg'] = $e->GetMessage();
            // handle/log the exception $e->GetMessage()
        }
        return $data;
    }

    /*
     * get card id
     */
    private function getCardId($registeration_id)
    {
        // get card id
        try {
            $CardRegistration = $this->mangopay->CardRegistrations->Get($registeration_id);
            $data['status'] = 200;
            $data['msg'] = 'Card added successfully.';
            $data['data'] = $CardRegistration;
            return $data;
        } catch (MangoPay\Libraries\ResponseException $e) {
            $data['status'] = 400;
            $data['msg'] = $e->GetErrorDetails();
            return $data;
            // handle/log the response exception with code $e->GetCode(), message $e->GetMessage() and error(s) $e->GetErrorDetails()
        } catch (MangoPay\Libraries\Exception $e) {
            $data['status'] = 400;
            $data['msg'] = $e->GetMessage();
            return $data;
            // handle/log the exception $e->GetMessage()
        }
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     *
     *
     *  @SWG\Get(
     *   path="/mangopay/getUserCards",
     *   summary="get notifications of a user",
     *   consumes={"multipart/form-data"},
     *   produces={"application/json"},
     *   tags={"Payments"},
     *   @SWG\Parameter(
     *     name="Authorization",
     *     in="header",
     *     required=false,
     *     description = "Enter Authorization Token",
     *     type="string"
     *   ),
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")
     * )
     *
     */
    public function getUserCards(Request $request)
    {
        $requested_data = $request->all();
        $user_mangopay_id = UserPaymentInfo::where('user_id', $requested_data['user_id'])->first();

        if ($user_mangopay_id) {
            try {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_URL, "https://api.sandbox.mangopay.com/v2.01/igniva44/users/" . $user_mangopay_id->mangopay_user_id . "/cards/");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                //  curl_setopt($ch, CURLOPT_POSTFIELDS, "page=1&per_page=25&sort=CreationDate:DESC");

                $headers = array();
                $headers[] = "Content-Type: application/json";
                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json',
                    'Authorization: Basic aWduaXZhNDQ6bm42dk1VMTFhcnQxS29MZ3dGR2hRQnhEbUU5bUswR2VmTHl4Z3NvMWVjNjM5UkNxSFY='));

                $result1 = curl_exec($ch);
                $result1 = json_decode($result1);
                if (curl_errno($ch)) {
                    // echo 'Error:' . curl_error($ch);
                    $data['status'] = 400;
                    $data['msg'] = curl_errno($ch);
                    return $data;
                } else {
                    $data['status'] = 200;
                    $data['msg'] = 'Cards fetched successfully.';
                    $data['data'] = $result1;
                    return $data;
                }
            } catch (MangoPay\Libraries\ResponseException $e) {
                $data['status'] = 400;
                $data['msg'] = $e->GetErrorDetails();
                return $data;
                // handle/log the response exception with code $e->GetCode(), message $e->GetMessage() and error(s) $e->GetErrorDetails()
            } catch (MangoPay\Libraries\Exception $e) {
                $data['status'] = 400;
                $data['msg'] = $e->GetMessage();
                return $data;
                // handle/log the exception $e->GetMessage()
            }
        } else {
            $msg['status'] = 400;
            $msg['msg'] = "No mangopay id of user found.";
            return $msg;
        }
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     *
     *
     *  @SWG\Post(
     *   path="/mangopay/deleteCard",
     *   summary="delete user cards on mangopay",
     *   consumes={"multipart/form-data"},
     *   produces={"application/json"},
     *   tags={"Payments"},
     * @SWG\Parameter(
     *     name="Authorization",
     *     in="header",
     *     required=false,
     *     description = "Enter Authorization Token",
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="card_id",
     *     in="formData",
     *     required=true,
     *     type="number",
     *     description = "Card id",
     *   ),
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")
     * )
     *
     */
    public function deleteCard(Request $request)
    {
        $requested_data = $request->all();
        $user_mangopay_id = UserPaymentInfo::where('user_id', $requested_data['user_id'])->first();
        if ($user_mangopay_id) {
            try {
                $Card = new \MangoPay\Card();
                $Card->Id = $requested_data['card_id'];
                $Card->Active = false;

                $Result = $this->mangopay->Cards->Update($Card);

                UserCard::where('card_id', $requested_data['card_id'])->update(['status' => 2]);

                $msg['status'] = 200;
                $msg['msg'] = 'Card deleted successfully.';
                return $msg;
            } catch (MangoPay\Libraries\ResponseException $e) {
                $msg['status'] = 400;
                $msg['msg'] = $e->GetErrorDetails();
                return $msg;
                // handle/log the response exception with code
                // $e->GetCode(), message $e->GetMessage() and error(s) $e->GetErrorDetails()
            } catch (MangoPay\Libraries\Exception $e) {
                $msg['status'] = 400;
                $msg['msg'] = $e->GetMessage();
                return $msg;
                // handle/log the exception $e->GetMessage()
            }
        } else {
            $msg['status'] = 400;
            $msg['msg'] = "No mangopay id of user found.";
            return $msg;
        }
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     *
     *
     *  @SWG\POST(
     *   path="/mangopay/createBank",
     *   summary="create user wallet on mango pay",
     *   consumes={"multipart/form-data"},
     *   produces={"application/json"},
     *   tags={"Payments"},
     * @SWG\Parameter(
     *     name="Authorization",
     *     in="header",
     *     required=false,
     *     description = "Enter Authorization Token",
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="type",
     *     in="formData",
     *     required=true,
     *     type="string",
     *     description = "Bank account type(IBAN/US/CA/GB/OTHER)",
     *   ),
     * @SWG\Parameter(
     *     name="tag",
     *     in="formData",
     *     required=true,
     *     type="string",
     *     description = "Enter any description",
     *   ),
     * @SWG\Parameter(
     *     name="addressline1",
     *     in="formData",
     *     required=true,
     *     type="string",
     *     description = "Addressline1",
     *   ),
     * @SWG\Parameter(
     *     name="addressline2",
     *     in="formData",
     *     required=false,
     *     type="string",
     *     description = "Addressline1",
     *   ),
     * @SWG\Parameter(
     *     name="city",
     *     in="formData",
     *     required=true,
     *     type="string",
     *     description = "City",
     *   ),
     * @SWG\Parameter(
     *     name="region",
     *     in="formData",
     *     required=true,
     *     type="string",
     *     description = "Region",
     *   ),
     * @SWG\Parameter(
     *     name="postal_code",
     *     in="formData",
     *     required=true,
     *     type="string",
     *     description = "Postal Code",
     *   ),
     * @SWG\Parameter(
     *     name="country",
     *     in="formData",
     *     required=true,
     *     type="string",
     *     description = "Country (Like - FR, DK etc)",
     *   ),
     *  @SWG\Parameter(
     *     name="owner_name",
     *     in="formData",
     *     required=true,
     *     type="string",
     *     description = "Bank account owner",
     *   ),
     * @SWG\Parameter(
     *     name="iban",
     *     in="formData",
     *     required=false,
     *     type="string",
     *     description = "Bank IBAN number",
     *   ),
     * @SWG\Parameter(
     *     name="bic",
     *     in="formData",
     *     required=false,
     *     type="string",
     *     description = "BIC of bank account",
     *   ),
     * @SWG\Parameter(
     *     name="account_number",
     *     in="formData",
     *     required=false,
     *     type="string",
     *     description = "Bank account number",
     *   ),
     * @SWG\Parameter(
     *     name="aba",
     *     in="formData",
     *     required=false,
     *     type="string",
     *     description = "ABA of Bank account",
     *   ),
     * @SWG\Parameter(
     *     name="deposit_account_type",
     *     in="formData",
     *     required=false,
     *     type="string",
     *     description = "The type of account",
     *   ),
     * @SWG\Parameter(
     *     name="branch_code",
     *     in="formData",
     *     required=false,
     *     type="string",
     *     description = "Bank branch code",
     *   ),
     * @SWG\Parameter(
     *     name="institution_number",
     *     in="formData",
     *     required=false,
     *     type="string",
     *     description = "Bank account institution number",
     *   ),
     * @SWG\Parameter(
     *     name="bank_name",
     *     in="formData",
     *     required=false,
     *     type="string",
     *     description = "Bank name",
     *   ),
     * @SWG\Parameter(
     *     name="sort_code",
     *     in="formData",
     *     required=false,
     *     type="string",
     *     description = "Sort code of bank account",
     *   ),
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")
     * )
     *
     */
    public function createBank(BankAccountRequest $request)
    {
        $requested_data = $request->all();
        $user_mangopay_id = UserPaymentInfo::where('user_id', $requested_data['user_id'])->first();

        switch ($requested_data['type']) {
            case 'IBAN':
                $createAccount = $this->createIbanAccount($user_mangopay_id->mangopay_user_id, $requested_data);
                break;
            case 'US':
                $createAccount = $this->createUsAccount($user_mangopay_id->mangopay_user_id, $requested_data);
                break;
            case 'CA':
                $createAccount = $this->createCaAccount($user_mangopay_id->mangopay_user_id, $requested_data);
                break;
            case 'GB':
                $createAccount = $this->createGbAccount($user_mangopay_id->mangopay_user_id, $requested_data);
                break;
            case 'OTHER':
                $createAccount = $this->createOtherAccount($user_mangopay_id->mangopay_user_id, $requested_data);
                break;
        }

        if ($createAccount['status'] == 200) {
            return $createAccount;
        } else {
            return $createAccount;
        }
    }

    // create IBAN bank account
    private function createIbanAccount($userId, $data)
    {
        $addressline2 = isset($data['addressline2']) ? $data['addressline2'] : '';
        $bic = isset($data['bic']) ? $data['bic'] : '';
        $bankObj = '{
            "Tag": "' . $data['tag'] . '",
            "OwnerAddress": {
            "AddressLine1": "' . $data['addressline1'] . '",
            "AddressLine2": "' . $addressline2 . '",
            "City": "' . $data['city'] . '",
            "Region": "' . $data['region'] . '",
            "PostalCode": "' . $data['postal_code'] . '",
            "Country": "' . $data['country'] . '"
            },
            "OwnerName": "' . $data['owner_name'] . '",
            "IBAN": "' . $data['iban'] . '",
            "BIC": "' . $bic . '"
        }';

        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_URL, "https://api.sandbox.mangopay.com/v2.01/igniva44/users/" . $userId . "/bankaccounts/iban/");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $bankObj);
            curl_setopt($ch, CURLOPT_POST, 1);

            $headers = array();
            $headers[] = "Content-Type: application/json";
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Authorization: Basic aWduaXZhNDQ6bm42dk1VMTFhcnQxS29MZ3dGR2hRQnhEbUU5bUswR2VmTHl4Z3NvMWVjNjM5UkNxSFY='));

            $result1 = curl_exec($ch);
            $result1 = json_decode($result1);
            if (curl_errno($ch)) {
                // echo 'Error:' . curl_error($ch);
                $data['status'] = 400;
                $data['msg'] = curl_errno($ch);
                return $data;
            } else {
                // save bank account in database
                $bankModel = [];
                $bankModel['user_id'] = $data['user_id'];
                $bankModel['bank_id'] = $result1->Id;
                $bankModel['type'] = "IBAN";
                $bankModel['tag'] = $data['tag'];
                $bankModel['addressline1'] = $data['addressline1'];
                $bankModel['addressline2'] = $addressline2;
                $bankModel['city'] = $data['city'];
                $bankModel['region'] = $data['region'];
                $bankModel['postal_code'] = $data['postal_code'];
                $bankModel['country'] = $data['country'];
                $bankModel['owner_name'] = $data['owner_name'];
                $bankModel['iban'] = $data['iban'];
                $bankModel['bic'] = $bic;
                $bankModel['status'] = 1;
                $bankModel['created_at'] = time();
                $bankModel['updated_at'] = time();
                UserBankAccount::create($bankModel);

                $data['status'] = 200;
                $data['msg'] = 'Account created successfully.';
                $data['data'] = $result1->Id;
                return $data;
            }
        } catch (MangoPay\Libraries\ResponseException $e) {
            $data['status'] = 400;
            $data['msg'] = $e->GetErrorDetails();
            return $data;
            // handle/log the response exception with code $e->GetCode(), message $e->GetMessage() and error(s) $e->GetErrorDetails()
        } catch (MangoPay\Libraries\Exception $e) {
            $data['status'] = 400;
            $data['msg'] = $e->GetMessage();
            return $data;
            // handle/log the exception $e->GetMessage()
        }

    }

    // create US bank account
    private function createUsAccount($userId, $data)
    {
        $addressline2 = isset($data['addressline2']) ? $data['addressline2'] : '';
        $deposit = isset($data['deposit_account_type']) ? $data['deposit_account_type'] : '';
        $bankObj = '{
            "Tag": "' . $data['tag'] . '",
            "OwnerAddress": {
            "AddressLine1": "' . $data['addressline1'] . '",
            "AddressLine2": "' . $addressline2 . '",
            "City": "' . $data['city'] . '",
            "Region": "' . $data['region'] . '",
            "PostalCode": "' . $data['postal_code'] . '",
            "Country": "' . $data['country'] . '"
            },
            "OwnerName": "' . $data['owner_name'] . '",
            "AccountNumber": "' . $data['account_number'] . '",
            "ABA": "' . $data['aba'] . '",
            "DepositAccountType": "' . $deposit . '"
        }';

        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_URL, "https://api.sandbox.mangopay.com/v2.01/igniva44/users/" . $userId . "/bankaccounts/us/");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $bankObj);
            curl_setopt($ch, CURLOPT_POST, 1);

            $headers = array();
            $headers[] = "Content-Type: application/json";
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Authorization: Basic aWduaXZhNDQ6bm42dk1VMTFhcnQxS29MZ3dGR2hRQnhEbUU5bUswR2VmTHl4Z3NvMWVjNjM5UkNxSFY='));

            $result1 = curl_exec($ch);
            $result1 = json_decode($result1);
            if (curl_errno($ch)) {
                echo "here";die;
                // echo 'Error:' . curl_error($ch);
                $data['status'] = 400;
                $data['msg'] = curl_errno($ch);
                return $data;
            } else {
                // save bank account in database
                $bankModel = [];
                $bankModel['user_id'] = $data['user_id'];
                $bankModel['bank_id'] = $result1->Id;
                $bankModel['type'] = "US";
                $bankModel['tag'] = $data['tag'];
                $bankModel['addressline1'] = $data['addressline1'];
                $bankModel['addressline2'] = $addressline2;
                $bankModel['city'] = $data['city'];
                $bankModel['region'] = $data['region'];
                $bankModel['postal_code'] = $data['postal_code'];
                $bankModel['country'] = $data['country'];
                $bankModel['owner_name'] = $data['owner_name'];
                $bankModel['account_number'] = $data['account_number'];
                $bankModel['aba'] = $data['aba'];
                $bankModel['deposit_account_type'] = $deposit;
                $bankModel['status'] = 1;
                $bankModel['created_at'] = time();
                $bankModel['updated_at'] = time();
                UserBankAccount::create($bankModel);

                $data['status'] = 200;
                $data['msg'] = 'Account created successfully.';
                $data['data'] = $result1->Id;
                return $data;
            }
        } catch (MangoPay\Libraries\ResponseException $e) {
            echo "here1";die;
            $data['status'] = 400;
            $data['msg'] = $e->GetErrorDetails();
            return $data;
            // handle/log the response exception with code $e->GetCode(), message $e->GetMessage() and error(s) $e->GetErrorDetails()
        } catch (MangoPay\Libraries\Exception $e) {
            echo "here2";die;
            $data['status'] = 400;
            $data['msg'] = $e->GetMessage();
            return $data;
            // handle/log the exception $e->GetMessage()
        }
    }

    // create CA bank account
    private function createCaAccount($userId, $data)
    {
        $addressline2 = isset($data['addressline2']) ? $data['addressline2'] : '';
        $bankObj = '{
            "Tag": "' . $data['tag'] . '",
            "OwnerAddress": {
            "AddressLine1": "' . $data['addressline1'] . '",
            "AddressLine2": "' . $addressline2 . '",
            "City": "' . $data['city'] . '",
            "Region": "' . $data['region'] . '",
            "PostalCode": "' . $data['postal_code'] . '",
            "Country": "' . $data['country'] . '"
            },
            "OwnerName": "' . $data['owner_name'] . '",
            "BranchCode":"' . $data['branch_code'] . '",
            "AccountNumber": "' . $data['account_number'] . '",
            "InstitutionNumber": "' . $data['institution_number'] . '"
            "BankName": "' . $data['bank_name'] . '"
        }';

        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_URL, "https://api.sandbox.mangopay.com/v2.01/igniva44/users/" . $userId . "/bankaccounts/ca/");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $bankObj);
            curl_setopt($ch, CURLOPT_POST, 1);

            $headers = array();
            $headers[] = "Content-Type: application/json";
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Authorization: Basic aWduaXZhNDQ6bm42dk1VMTFhcnQxS29MZ3dGR2hRQnhEbUU5bUswR2VmTHl4Z3NvMWVjNjM5UkNxSFY='));

            $result1 = curl_exec($ch);
            $result1 = json_decode($result1);
            if (curl_errno($ch)) {
                // echo 'Error:' . curl_error($ch);
                $data['status'] = 400;
                $data['msg'] = curl_errno($ch);
                return $data;
            } else {
                // save bank account in database
                $bankModel = [];
                $bankModel['user_id'] = $data['user_id'];
                $bankModel['bank_id'] = $result1->Id;
                $bankModel['type'] = "CA";
                $bankModel['tag'] = $data['tag'];
                $bankModel['addressline1'] = $data['addressline1'];
                $bankModel['addressline2'] = $addressline2;
                $bankModel['city'] = $data['city'];
                $bankModel['region'] = $data['region'];
                $bankModel['postal_code'] = $data['postal_code'];
                $bankModel['country'] = $data['country'];
                $bankModel['owner_name'] = $data['owner_name'];
                $bankModel['account_number'] = $data['account_number'];
                $bankModel['branch_code'] = $data['branch_code'];
                $bankModel['institution_number'] = $data['institution_number'];
                $bankModel['bank_name'] = $data['bank_name'];
                $bankModel['status'] = 1;
                $bankModel['created_at'] = time();
                $bankModel['updated_at'] = time();
                UserBankAccount::create($bankModel);

                $data['status'] = 200;
                $data['msg'] = 'Account created successfully.';
                $data['data'] = $result1->Id;
                return $data;
            }
        } catch (MangoPay\Libraries\ResponseException $e) {
            $data['status'] = 400;
            $data['msg'] = $e->GetErrorDetails();
            return $data;
            // handle/log the response exception with code $e->GetCode(), message $e->GetMessage() and error(s) $e->GetErrorDetails()
        } catch (MangoPay\Libraries\Exception $e) {
            $data['status'] = 400;
            $data['msg'] = $e->GetMessage();
            return $data;
            // handle/log the exception $e->GetMessage()
        }
    }

    // create GB bank account
    private function createGbAccount($userId, $data)
    {
        $addressline2 = isset($data['addressline2']) ? $data['addressline2'] : '';
        $bankObj = '{
            "Tag": "' . $data['tag'] . '",
            "OwnerAddress": {
            "AddressLine1": "' . $data['addressline1'] . '",
            "AddressLine2": "' . $addressline2 . '",
            "City": "' . $data['city'] . '",
            "Region": "' . $data['region'] . '",
            "PostalCode": "' . $data['postal_code'] . '",
            "Country": "' . $data['country'] . '"
            },
            "OwnerName": "' . $data['owner_name'] . '",
            "SortCode":"' . $data['sort_code'] . '",
            "AccountNumber": "' . $data['account_number'] . '",
        }';

        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_URL, "https://api.sandbox.mangopay.com/v2.01/igniva44/users/" . $userId . "/bankaccounts/gb/");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $bankObj);
            curl_setopt($ch, CURLOPT_POST, 1);

            $headers = array();
            $headers[] = "Content-Type: application/json";
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Authorization: Basic aWduaXZhNDQ6bm42dk1VMTFhcnQxS29MZ3dGR2hRQnhEbUU5bUswR2VmTHl4Z3NvMWVjNjM5UkNxSFY='));

            $result1 = curl_exec($ch);
            $result1 = json_decode($result1);

            if (curl_errno($ch)) {
                // echo 'Error:' . curl_error($ch);
                $data['status'] = 400;
                $data['msg'] = curl_errno($ch);
                return $data;
            } else {
                // save bank account in database
                $bankModel = [];
                $bankModel['user_id'] = $data['user_id'];
                $bankModel['bank_id'] = $result1->Id;
                $bankModel['type'] = "GB";
                $bankModel['tag'] = $data['tag'];
                $bankModel['addressline1'] = $data['addressline1'];
                $bankModel['addressline2'] = $addressline2;
                $bankModel['city'] = $data['city'];
                $bankModel['region'] = $data['region'];
                $bankModel['postal_code'] = $data['postal_code'];
                $bankModel['country'] = $data['country'];
                $bankModel['owner_name'] = $data['owner_name'];
                $bankModel['account_number'] = $data['account_number'];
                $bankModel['sort_code'] = $data['sort_code'];
                $bankModel['status'] = 1;
                $bankModel['created_at'] = time();
                $bankModel['updated_at'] = time();
                UserBankAccount::create($bankModel);

                $data['status'] = 200;
                $data['msg'] = 'Account created successfully.';
                $data['data'] = $result1->Id;
                return $data;
            }
        } catch (MangoPay\Libraries\ResponseException $e) {
            $data['status'] = 400;
            $data['msg'] = $e->GetErrorDetails();
            return $data;
            // handle/log the response exception with code $e->GetCode(), message $e->GetMessage() and error(s) $e->GetErrorDetails()
        } catch (MangoPay\Libraries\Exception $e) {
            $data['status'] = 400;
            $data['msg'] = $e->GetMessage();
            return $data;
            // handle/log the exception $e->GetMessage()
        }
    }

    // create OTHER bank account
    private function createOtherAccount($userId, $data)
    {
        $addressline2 = isset($data['addressline2']) ? $data['addressline2'] : '';
        $bankObj = '{
            "Tag": "' . $data['tag'] . '",
            "OwnerAddress": {
            "AddressLine1": "' . $data['addressline1'] . '",
            "AddressLine2": "' . $addressline2 . '",
            "City": "' . $data['city'] . '",
            "Region": "' . $data['region'] . '",
            "PostalCode": "' . $data['postal_code'] . '",
            "Country": "' . $data['country'] . '"
            },
            "OwnerName": "' . $data['owner_name'] . '",
            "Country":"' . $data['country'] . '",
            "AccountNumber": "' . $data['account_number'] . '",
            "BIC": "' . $data['bic'] . '",
        }';

        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_URL, "https://api.sandbox.mangopay.com/v2.01/igniva44/users/" . $userId . "/bankaccounts/other/");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $bankObj);
            curl_setopt($ch, CURLOPT_POST, 1);

            $headers = array();
            $headers[] = "Content-Type: application/json";
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Authorization: Basic aWduaXZhNDQ6bm42dk1VMTFhcnQxS29MZ3dGR2hRQnhEbUU5bUswR2VmTHl4Z3NvMWVjNjM5UkNxSFY='));

            $result1 = curl_exec($ch);
            $result1 = json_decode($result1);
            if (curl_errno($ch)) {
                // echo 'Error:' . curl_error($ch);
                $data['status'] = 400;
                $data['msg'] = curl_errno($ch);
                return $data;
            } else {
                // save bank account in database
                $bankModel = [];
                $bankModel['user_id'] = $data['user_id'];
                $bankModel['bank_id'] = $result1->Id;
                $bankModel['type'] = "OTHER";
                $bankModel['tag'] = $data['tag'];
                $bankModel['addressline1'] = $data['addressline1'];
                $bankModel['addressline2'] = $addressline2;
                $bankModel['city'] = $data['city'];
                $bankModel['region'] = $data['region'];
                $bankModel['postal_code'] = $data['postal_code'];
                $bankModel['country'] = $data['country'];
                $bankModel['owner_name'] = $data['owner_name'];
                $bankModel['account_number'] = $data['account_number'];
                $bankModel['bic'] = $data['bic'];
                $bankModel['status'] = 1;
                $bankModel['created_at'] = time();
                $bankModel['updated_at'] = time();
                UserBankAccount::create($bankModel);

                $data['status'] = 200;
                $data['msg'] = 'Account created successfully.';
                $data['data'] = $result1->Id;
                return $data;
            }
        } catch (MangoPay\Libraries\ResponseException $e) {
            $data['status'] = 400;
            $data['msg'] = $e->GetErrorDetails();
            return $data;
            // handle/log the response exception with code $e->GetCode(), message $e->GetMessage() and error(s) $e->GetErrorDetails()
        } catch (MangoPay\Libraries\Exception $e) {
            $data['status'] = 400;
            $data['msg'] = $e->GetMessage();
            return $data;
            // handle/log the exception $e->GetMessage()
        }
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     *
     *
     *  @SWG\POST(
     *   path="/mangopay/deleteBankAccount",
     *   summary="delete user bank account on mangopay",
     *   consumes={"multipart/form-data"},
     *   produces={"application/json"},
     *   tags={"Payments"},
     * @SWG\Parameter(
     *     name="Authorization",
     *     in="header",
     *     required=false,
     *     description = "Enter Authorization Token",
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="bank_id",
     *     in="formData",
     *     required=true,
     *     type="number",
     *     description = "Bank Id",
     *   ),
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")
     * )
     *
     */
    public function deleteBankAccount(Request $request)
    {
        $requested_data = $request->all();
        $user_mangopay_id = UserPaymentInfo::where('user_id', $requested_data['user_id'])->first();
        if ($user_mangopay_id) {

            try {
                $bankAccount = $this->mangopay->Users->GetBankAccount($user_mangopay_id->mangopay_user_id, $requested_data['bank_id']);
                $bankAccount->Active = false;
                $result = $this->mangopay->Users->UpdateBankAccount($user_mangopay_id->mangopay_user_id, $bankAccount);

                UserBankAccount::where('bank_id', $requested_data['bank_id'])->update(['status' => 2]);

                $data['status'] = 200;
                $data['msg'] = 'Bank account deleted successfully.';
                return $data;
            } catch (MangoPay\Libraries\ResponseException $e) {
                $data['status'] = 400;
                $data['msg'] = $e->GetErrorDetails();
                return $data;
                // handle/log the response exception with code $e->GetCode(), message $e->GetMessage() and error(s) $e->GetErrorDetails()
            } catch (MangoPay\Libraries\Exception $e) {
                $data['status'] = 400;
                $data['msg'] = $e->GetMessage();
                return $data;
                // handle/log the exception $e->GetMessage()
            }
        } else {
            $msg['status'] = 400;
            $msg['msg'] = "No mangopay id of user found.";
            return $msg;
        }
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     *
     *
     *  @SWG\Get(
     *   path="/mangopay/getUserBankAccount",
     *   summary="get user all bank accounts",
     *   consumes={"multipart/form-data"},
     *   produces={"application/json"},
     *   tags={"Payments"},
     *   @SWG\Parameter(
     *     name="Authorization",
     *     in="header",
     *     required=false,
     *     description = "Enter Authorization Token",
     *     type="string"
     *   ),
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")
     * )
     *
     */
    public function getUserBankAccount(Request $request)
    {
        $requested_data = $request->all();
        $user_mangopay_id = UserPaymentInfo::where('user_id', $requested_data['user_id'])->first();
        if ($user_mangopay_id) {

            try {

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_URL, "https://api.sandbox.mangopay.com/v2.01/igniva44/users/" . $user_mangopay_id->mangopay_user_id . "/bankaccounts/");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

                $headers = array();
                $headers[] = "Content-Type: application/json";
                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json',
                    'Authorization: Basic aWduaXZhNDQ6bm42dk1VMTFhcnQxS29MZ3dGR2hRQnhEbUU5bUswR2VmTHl4Z3NvMWVjNjM5UkNxSFY='));

                $result1 = curl_exec($ch);
                $result1 = json_decode($result1);
                if (curl_errno($ch)) {
                    // echo 'Error:' . curl_error($ch);
                    $data['status'] = 400;
                    $data['msg'] = curl_errno($ch);
                    return $data;
                } else {

                    $data['status'] = 200;
                    $data['msg'] = 'User bank accounts fetched successfully.';
                    $data['data'] = $result1;
                    return $data;
                }
            } catch (MangoPay\Libraries\ResponseException $e) {
                $data['status'] = 400;
                $data['msg'] = $e->GetErrorDetails();
                return $data;
                // handle/log the response exception with code $e->GetCode(), message $e->GetMessage() and error(s) $e->GetErrorDetails()
            } catch (MangoPay\Libraries\Exception $e) {
                $data['status'] = 400;
                $data['msg'] = $e->GetMessage();
                return $data;
                // handle/log the exception $e->GetMessage()
            }
        } else {
            $msg['status'] = 400;
            $msg['msg'] = "No mangopay id of user found.";
            return $msg;
        }
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     *
     *
     *  @SWG\POST(
     *   path="/mangopay/createDirectPayIn",
     *   summary="debit amount from customer card and save into customer wallet",
     *   consumes={"multipart/form-data"},
     *   produces={"application/json"},
     *   tags={"Payments"},
     * @SWG\Parameter(
     *     name="Authorization",
     *     in="header",
     *     required=false,
     *     description = "Enter Authorization Token",
     *     type="string"
     *   ),
     *  @SWG\Parameter(
     *     name="debit_user_mangopay_id",
     *     in="formData",
     *     required=true,
     *     type="number",
     *     description = "payer user mangopay user id",
     *   ),
     * @SWG\Parameter(
     *     name="debited_user_id",
     *     in="formData",
     *     required=true,
     *     type="number",
     *     description = "payer user  id",
     *   ),
     * @SWG\Parameter(
     *     name="credited_user_mangopay_id",
     *     in="formData",
     *     required=true,
     *     type="number",
     *     description = "payee user mangopay id",
     *   ),
     * @SWG\Parameter(
     *     name="credited_user_id",
     *     in="formData",
     *     required=true,
     *     type="number",
     *     description = "payee user  id",
     *   ),
     * @SWG\Parameter(
     *     name="discount",
     *     in="formData",
     *     required=true,
     *     type="number",
     *     description = "discount",
     *   ),
     * @SWG\Parameter(
     *     name="charge",
     *     in="formData",
     *     required=true,
     *     type="number",
     *     description = "charge",
     *   ),
     * @SWG\Parameter(
     *     name="amount",
     *     in="formData",
     *     required=true,
     *     type="number",
     *     description = "amount",
     *   ),
     * @SWG\Parameter(
     *     name="fee_amount",
     *     in="formData",
     *     required=true,
     *     type="number",
     *     description = "amount",
     *   ),
     * @SWG\Parameter(
     *     name="currency",
     *     in="formData",
     *     required=true,
     *     type="number",
     *     description = "currency",
     *   ),
     *   @SWG\Parameter(
     *     name="invitation_id",
     *     in="formData",
     *     required=true,
     *     type="number",
     *     description = "invitation_id",
     *   ),
     * @SWG\Parameter(
     *     name="card_id",
     *     in="formData",
     *     required=true,
     *     type="number",
     *     description = "card id",
     *   ),
     * @SWG\Parameter(
     *     name="payerWallet",
     *     in="formData",
     *     required=true,
     *     type="number",
     *     description = "card id",
     *   ),
     * @SWG\Parameter(
     *     name="payeeWallet",
     *     in="formData",
     *     required=true,
     *     type="number",
     *     description = "card id",
     *   ),
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")
     * )
     *
     */
    public function createDirectPayIn(Request $request)
    {

        $requested_data = $request->all();

        // if not discount & no charge
        if (($requested_data['discount'] == 0 || $requested_data['discount'] < 0) && ($requested_data['charge'] == 0 || $requested_data['charge'] < 0)) {
            $total_amount = $requested_data['amount'];
            $debited_amount = $requested_data['amount'];
            $fee = $requested_data['fee_amount'];
            $invoice_type = 0;
        }

        // if discount & no charge
        if ($requested_data['discount'] > 0 && ($requested_data['charge'] == 0 || $requested_data['charge'] < 0)) {
            $total_amount = $requested_data['amount'];
            $debited_amount = $requested_data['amount'] - $requested_data['discount'];
            $fee = $requested_data['fee_amount'] - $requested_data['discount'];
            $invoice_type = 1;
        }

        // if no discount but charge
        if (($requested_data['discount'] == 0 || $requested_data['discount'] < 0) && $requested_data['charge'] > 0) {
            $total_amount = $requested_data['amount'];
            $debited_amount = $requested_data['amount'];
            $fee = $requested_data['fee_amount'] + $requested_data['charge'];
            $invoice_type = 2;
        }

        // if discount & charge both
        if ($requested_data['discount'] > 0 && $requested_data['charge'] > 0) {
            $total_amount = $requested_data['amount'];
            $debited_amount = $requested_data['amount'] - $requested_data['discount'];
            $fee = ($requested_data['fee_amount'] - $requested_data['discount']) + $requested_data['charge'];
            $invoice_type = 3;
        }

        try {
            $payIn = new \MangoPay\PayIn();
            $payIn->CreditedWalletId = $requested_data['payerWallet']; //credit money from payer card to payer wallet
            $payIn->AuthorId = $requested_data['debit_user_mangopay_id']; //$requested_data['author_id']; #debited user id
            $payIn->DebitedFunds = new \MangoPay\Money();
            $payIn->DebitedFunds->Amount = $debited_amount; //$requested_data['amount']; //$requested_data['debit_funds']; #amount to be debit from card
            $payIn->DebitedFunds->Currency = $requested_data['currency']; #curreny
            $payIn->Fees = new \MangoPay\Money();
            $payIn->Fees->Amount = 0;
            $payIn->Fees->Currency = $requested_data['currency'];

            // payment type as CARD
            $payIn->PaymentDetails = new \MangoPay\PayInPaymentDetailsCard();
            $payIn->PaymentDetails->CardId = $requested_data['card_id']; #card id gnerated at front end
            // execution type as DIRECT
            $payIn->ExecutionDetails = new \MangoPay\PayInExecutionDetailsDirect();
            $payIn->ExecutionDetails->SecureModeReturnURL = 'http://test.com';
            // create Pay-In
            $createdPayIn = $this->mangopay->PayIns->Create($payIn);
            if ($createdPayIn->Status == \MangoPay\PayInStatus::Succeeded) {
                $cardToWallet = $this->cardToWallet($requested_data, $debited_amount, $fee, $invoice_type);
                $artistPayementInitatied = $this->artistPayementInitatied($requested_data, $debited_amount, $fee, $invoice_type);

                // save in payment table
                $transcationModel = [];
                $transcationModel['payer_id'] = $requested_data['debited_user_id'];
                $transcationModel['payee_id'] = $requested_data['debited_user_id'];
                $transcationModel['total_amount'] = $requested_data['amount'];
                $transcationModel['amount_paid'] = $requested_data['amount'];
                $transcationModel['commission_amount'] = 0;
                $transcationModel['payment_date'] = $createdPayIn->ExecutionDate;
                $transcationModel['payment_status'] = 'payment_succeeded';
                $transcationModel['transaction_id'] = $createdPayIn->Id;
                $transcationModel['status'] = 1;
                $transcationModel['created_at'] = time();
                $transcationModel['updated_at'] = time();

                Payment::create($transcationModel);

                $data = [];
                $data['status'] = 200;
                $data['msg'] = 'Payment success';
                return Response::json($data);
            } else {
                $data['status'] = 400;
                $data['msg'] = 'Error in deducting payment';
                return $data;
            }
        } catch (MangoPay\Libraries\ResponseException $e) {
            $data['status'] = 400;
            $data['msg'] = $e->GetErrorDetails();
            return $data;
            // handle/log the response exception with code $e->GetCode(), message $e->GetMessage() and error(s) $e->GetErrorDetails()
        } catch (MangoPay\Libraries\Exception $e) {
            $data['status'] = 400;
            $data['msg'] = $e->GetMessage();
            return $data;
            // handle/log the exception $e->GetMessage()
        }
    }

    private function cardToWallet($data, $debited_amount, $fee, $invoice_type)
    {
        if ($data['discount'] > 0) {
            $discount_charge = 1;
            $commision = $data['fee_amount'] - $data['discount'];
        } else {
            $discount_charge = 0;
            $commision = $data['fee_amount'] - 0;
        }
        $invoiceModel = [];
        $invoiceModel['payer_id'] = $data['debited_user_id'];
        $invoiceModel['payee_id'] = $data['debited_user_id'];
        $invoiceModel['total_amount'] = $data['amount'];
        $invoiceModel['paid_amount'] = $debited_amount;
        $invoiceModel['commission_amount'] = $commision;
        $invoiceModel['date_of_payment'] = time();
        $invoiceModel['discount'] = $data['discount'];
        $invoiceModel['charge'] = 0;
        $invoiceModel['payment_type'] = 'payin';
        $invoiceModel['payment_method'] = 'card_to_wallet';
        $invoiceModel['payment_status'] = 'success';
        $invoiceModel['discount_charge'] = $discount_charge;
        $invoiceModel['status'] = 1;
        $invoiceModel['created_at'] = time();
        $invoiceModel['updated_at'] = time();
        $invoiceModel['invitation_id'] = $data['invitation_id'];

        $this->genrateInvoices($invoiceModel);
        return true;
    }

    private function artistPayementInitatied($data, $debited_amount, $fee, $invoice_type)
    {
        if ($data['charge'] > 0) {
            $discount_charge = 2;
            $amount = $data['amount'] - $data['fee_amount'];
            $commision = $data['fee_amount'] + $data['charge'];

        } else {
            $discount_charge = 0;
            $amount = $data['amount'] - $data['fee_amount'];
            $commision = $data['fee_amount'] + 0;
        }
        $invoiceModel = [];
        $invoiceModel['payer_id'] = $data['debited_user_id'];
        $invoiceModel['payee_id'] = $data['credited_user_id'];
        $invoiceModel['total_amount'] = $data['amount'];
        $invoiceModel['paid_amount'] = $amount;
        $invoiceModel['commission_amount'] = $commision;
        $invoiceModel['date_of_payment'] = time();
        $invoiceModel['discount'] = 0;
        $invoiceModel['charge'] = $data['charge'];
        $invoiceModel['payment_type'] = 'invoice_genrate';
        $invoiceModel['payment_method'] = 'card_to_wallet';
        $invoiceModel['payment_status'] = 'payment_initatied';
        $invoiceModel['discount_charge'] = $discount_charge;
        $invoiceModel['status'] = 1;
        $invoiceModel['created_at'] = time();
        $invoiceModel['updated_at'] = time();
        $invoiceModel['invitation_id'] = $data['invitation_id'];

        $this->genrateInvoices($invoiceModel);
        return true;
    }

    private function genrateInvoices($data)
    {
        Invoice::create($data);
        return true;
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     *
     *
     *  @SWG\POST(
     *   path="/mangopay/releasePayment",
     *   summary="release payment from customer wallet to artist wallet and admin with discount & charge calculation and genrate invoice and transcations",
     *   consumes={"multipart/form-data"},
     *   produces={"application/json"},
     *   tags={"Payments"},
     * @SWG\Parameter(
     *     name="Authorization",
     *     in="header",
     *     required=false,
     *     description = "Enter Authorization Token",
     *     type="string"
     *   ),
     * 
     *  @SWG\Parameter(
     *     name="debit_user_mangopay_id",
     *     in="formData",
     *     required=true,
     *     type="number",
     *     description = "payer user mangopay user id",
     *   ),
     * @SWG\Parameter(
     *     name="debited_user_id",
     *     in="formData",
     *     required=true,
     *     type="number",
     *     description = "payer user  id",
     *   ),
     * @SWG\Parameter(
     *     name="credited_user_mangopay_id",
     *     in="formData",
     *     required=true,
     *     type="number",
     *     description = "payee user mangopay id",
     *   ),
     * @SWG\Parameter(
     *     name="credited_user_id",
     *     in="formData",
     *     required=true,
     *     type="number",
     *     description = "payee user  id",
     *   ),
     * @SWG\Parameter(
     *     name="discount",
     *     in="formData",
     *     required=true,
     *     type="number",
     *     description = "discount",
     *   ),
     * @SWG\Parameter(
     *     name="charge",
     *     in="formData",
     *     required=true,
     *     type="number",
     *     description = "charge",
     *   ),
     * @SWG\Parameter(
     *     name="amount",
     *     in="formData",
     *     required=true,
     *     type="number",
     *     description = "amount",
     *   ),
     * @SWG\Parameter(
     *     name="fee_amount",
     *     in="formData",
     *     required=true,
     *     type="number",
     *     description = "amount",
     *   ),
     * @SWG\Parameter(
     *     name="currency",
     *     in="formData",
     *     required=true,
     *     type="number",
     *     description = "currency",
     *   ),
     *   @SWG\Parameter(
     *     name="invitation_id",
     *     in="formData",
     *     required=true,
     *     type="number",
     *     description = "invitation_id",
     *   ),
     * @SWG\Parameter(
     *     name="payerWallet",
     *     in="formData",
     *     required=true,
     *     type="number",
     *     description = "card id",
     *   ),
     * @SWG\Parameter(
     *     name="payeeWallet",
     *     in="formData",
     *     required=true,
     *     type="number",
     *     description = "card id",
     *   ),
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")
     * )
     *
     */
    public function releasePayment(Request $request)
    {
        $requested_data = $request->all();
        $requested_data = [];
        $requested_data['debit_user_mangopay_id'] = '61839868';
        $requested_data['debited_user_id'] = 3;
        $requested_data['credited_user_mangopay_id'] = '61759539';
        $requested_data['credited_user_id'] = 2;
        $requested_data['discount'] = 0;
        $requested_data['charge'] = 0;
        $requested_data['amount'] = 2000;
        $requested_data['fee_amount'] = 40;
        $requested_data['currency'] = 'GBP';
        $requested_data['invitation_id'] = 14;
        $requested_data['payerWallet'] = 61839869;
        $requested_data['payeeWallet'] = 61968744;
        $requested_data['invitation_id'] = 14;

        // if not discount & no charge
        if (($requested_data['discount'] == 0 || $requested_data['discount'] < 0) && ($requested_data['charge'] == 0 || $requested_data['charge'] < 0)) {
            $debited_amount = $requested_data['amount'];
            $fee = $requested_data['fee_amount'];
            $invoice_type = 0;
            $transcation_amount = $requested_data['amount'] - $requested_data['fee_amount'];
        }

        // if discount & no charge
        if ($requested_data['discount'] > 0 && ($requested_data['charge'] == 0 || $requested_data['charge'] < 0)) {
            $debited_amount = $requested_data['amount'] - $requested_data['discount'];
            $fee = $requested_data['fee_amount'] - $requested_data['discount'];
            $invoice_type = 1;
            $transcation_amount = $requested_data['amount'] - $requested_data['fee_amount'];
        }

        // if no discount but charge
        if (($requested_data['discount'] == 0 || $requested_data['discount'] < 0) && $requested_data['charge'] > 0) {
            $debited_amount = $requested_data['amount'];
            $fee = $requested_data['fee_amount'] + $requested_data['charge'];
            $invoice_type = 2;
            $transcation_amount = $requested_data['amount'] - $requested_data['fee_amount'] - $requested_data['charge'];
        }

        // if discount & charge both
        if ($requested_data['discount'] > 0 && $requested_data['charge'] > 0) {
            $debited_amount = $requested_data['amount'] - $requested_data['discount'];
            $fee = ($requested_data['fee_amount'] - $requested_data['discount']) + $requested_data['charge'];
            $invoice_type = 3;
            $transcation_amount = $requested_data['amount'] - $requested_data['fee_amount'] - $requested_data['charge'];
        }

        try {
            $Transfer = new \MangoPay\Transfer();
            $Transfer->Tag = "Wallet to wallet transfer";
            $Transfer->AuthorId = $requested_data['debit_user_mangopay_id'];
            $Transfer->CreditedUserId = $requested_data['credited_user_mangopay_id'];
            $Transfer->DebitedFunds = new \MangoPay\Money();
            $Transfer->DebitedFunds->Currency = $requested_data['currency'];
            $Transfer->DebitedFunds->Amount = $debited_amount;
            $Transfer->Fees = new \MangoPay\Money();
            $Transfer->Fees->Currency = $requested_data['currency'];
            $Transfer->Fees->Amount = $fee;
            $Transfer->DebitedWalletId = $requested_data['payerWallet'];
            $Transfer->CreditedWalletId = $requested_data['payeeWallet'];
            $Result = $this->mangopay->Transfers->Create($Transfer);
            if ($Result->ResultMessage == 'Success') {

                // genrate invoice
                $this->customerDebitedInvoice($requested_data, $debited_amount, $fee, $invoice_type);
                $this->releaseToArtistInvoice($requested_data, $debited_amount, $fee, $invoice_type);
                // genrate transcation

                // save in payment table
                $transcationModel = [];
                $transcationModel['payer_id'] = $requested_data['debited_user_id'];
                $transcationModel['payee_id'] = $requested_data['credited_user_id'];
                $transcationModel['total_amount'] = $requested_data['amount'];
                $transcationModel['amount_paid'] = $transcation_amount;
                $transcationModel['commission_amount'] = $fee;
                $transcationModel['payment_date'] = $Result->ExecutionDate;
                $transcationModel['payment_status'] = 'payment_succeeded';
                $transcationModel['transaction_id'] = $Result->Id;
                $transcationModel['status'] = 1;
                $transcationModel['created_at'] = time();
                $transcationModel['updated_at'] = time();

                Payment::create($transcationModel);

                $msg['status'] = 200;
                $msg['msg'] = "Payment done successfully";
                $msg['data'] = $Result;
                return $msg;
            } else {
                $msg['status'] = 400;
                $msg['msg'] = "Unable to pay";
                return $msg;
            }
        } catch (MangoPay\Libraries\ResponseException $e) {
            $data['status'] = 400;
            $data['msg'] = $e->GetErrorDetails();
            return $data;
            // handle/log the response exception with code $e->GetCode(), message $e->GetMessage() and error(s) $e->GetErrorDetails()
        } catch (MangoPay\Libraries\Exception $e) {
            $data['status'] = 400;
            $data['msg'] = $e->GetMessage();
            return $data;
            // handle/log the exception $e->GetMessage()
        }
    }

    // relase payment to artist wallet invoice (Invoice for customer)
    private function customerDebitedInvoice($data, $debited_amount, $fee, $invoice_type)
    {
        
        if ($data['discount'] > 0) {
            $discount_charge = 1;

        } else {
            $discount_charge = 0;
        }
        $invoiceModel = [];

        $invoiceModel['payer_id'] = $data['debited_user_id'];
        $invoiceModel['payee_id'] = $data['credited_user_id'];
        $invoiceModel['total_amount'] = $data['amount'];
        $invoiceModel['paid_amount'] = $debited_amount;
        $invoiceModel['commission_amount'] = $fee;
        $invoiceModel['date_of_payment'] = time();
        $invoiceModel['discount'] = $data['discount'];
        $invoiceModel['charge'] = 0;
        $invoiceModel['payment_type'] = 'payment_debited';
        $invoiceModel['payment_method'] = 'wallet_to_wallet';
        $invoiceModel['payment_status'] = 'payment_paid';
        $invoiceModel['discount_charge'] = $discount_charge;
        $invoiceModel['status'] = 1;
        $invoiceModel['created_at'] = time();
        $invoiceModel['updated_at'] = time();
        $invoiceModel['invitation_id'] = $data['invitation_id'];

        $this->genrateInvoices($invoiceModel);
        return true;
    }

    // relase payment to artist wallet invoice (Invoice for artist)
    private function releaseToArtistInvoice($data, $debited_amount, $fee, $invoice_type)
    {
        $amount_paid = $data['amount'] - $data['fee_amount'];
        if ($data['charge'] > 0) {
            $discount_charge = 2;
        } else {
            $discount_charge = 0;
        }
        $invoiceModel = [];

        $invoiceModel['payer_id'] = $data['debited_user_id'];
        $invoiceModel['payee_id'] = $data['credited_user_id'];
        $invoiceModel['total_amount'] = $data['amount'];
        $invoiceModel['paid_amount'] = $amount_paid;
        $invoiceModel['commission_amount'] = $fee;
        $invoiceModel['date_of_payment'] = time();
        $invoiceModel['discount'] = 0;
        $invoiceModel['charge'] = $data['charge'];
        $invoiceModel['payment_type'] = 'payment_credited';
        $invoiceModel['payment_method'] = 'wallet_to_wallet';
        $invoiceModel['payment_status'] = 'payment_paid';
        $invoiceModel['discount_charge'] = $discount_charge;
        $invoiceModel['status'] = 1;
        $invoiceModel['created_at'] = time();
        $invoiceModel['updated_at'] = time();
        $invoiceModel['invitation_id'] = $data['invitation_id'];

        $this->genrateInvoices($invoiceModel);
        return true;
    }

}
