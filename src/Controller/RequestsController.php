<?php

namespace App\Controller;

use App\Controller\AppController; 
use Cake\Validation\Validator;
use Cake\Utility\Text;
use Cake\Core\Configure;

use Cake\Mailer\Mailer;

class RequestsController extends AppController{

    public function initialize(): void{
        parent::initialize();
        $this->loadModel('Requests');
        $this->loadComponent('Paginator');
    }

    /**
     * Ответ на отправку формы: JSON для fetch-запросов с сайта, иначе Flash + возврат на страницу.
     */
    private function __respond(bool $ok, string $message){
        if( $this->request->is('ajax') ){
            return $this->response
                ->withStatus($ok ? 200 : 422)
                ->withType('application/json')
                ->withStringBody(json_encode(['ok' => $ok, 'message' => $message], JSON_UNESCAPED_UNICODE));
        }
        $ok ? $this->Flash->success($message) : $this->Flash->error($message);
        return $this->redirect( $this->referer() );
    }

    public function send(){
        $model = 'Requests';
        date_default_timezone_set('Asia/Almaty');
		$data = $this->request->getData();
		if( empty($data['name']) || empty($data['phone']) ){
			return $this->__respond(false, __('Ошибка отправки'));
		}
		//if($this->__checkRecaptchaResponse($data['g-recaptcha-response'])){
        if( $this->request->is('post') ){
            $data['date'] = date('Y-m-d H:i:s');
            $data['type'] = 'request';

            $entity = $this->$model->newEntity($data);

            if( $entity->getErrors() ){
                $errors = $entity->getErrors();
                $first = reset($errors);
                return $this->__respond(false, (string)reset($first));
            }
			if(!isset($data['phone']) || empty($data['phone'])){
                $this->Session->setFlash('Введите пожалуйста телефон', 'default', array(), 'bad');
                return $this->redirect('/?status=error');
            }
            if(!isset($data['email']) || empty($data['email'])){
                $data['email'] = '-';
            }
            $b24_email = Array();
                if( isset($data['mail']) && $data['mail'] ){
                    $b24_email = Array(
                        0 => Array(
                            'VALUE' => $data['mail'],
                            "VALUE_TYPE" => "WORK",
                        ),
                    );
                }

            // формируем URL в переменной $queryUrl
            // $queryUrl = 'https://tarichenko.bitrix24.kz/rest/1/r4l7iz36ti88ljxd/crm.lead.add.json';
            $queryUrl = 'https://pangaea.bitrix24.kz/rest/11/ycx3i1l6pnunf7r1/crm.lead.add.json';

            // формируем параметры для создания лида в переменной $queryData
            $queryData = http_build_query(array(
              'fields' => array(
                'TITLE' => 'Заявка с сайта mzhk-astana.kz',
                'NAME' => $data['name'],
                'PHONE' => Array(
                    0 => Array(
                        "VALUE" => $data['phone'],
                        "VALUE_TYPE" => "WORK",
                    ),
                ),
                'EMAIL' => $b24_email,
                'COMMENTS' => isset($data['message']) ? (string)$data['message'] : '',
                'ADDRESS' => '',
                'SOURCE_ID' => 'WEBFORM',
              ),
              'params' => array("REGISTER_SONET_EVENT" => "Y")
            ));

            // обращаемся к Битрикс24 при помощи функции curl_exec
            $curl = curl_init();
            curl_setopt_array($curl, array(
              CURLOPT_SSL_VERIFYPEER => 0,
              CURLOPT_POST => 1,
              CURLOPT_HEADER => 0,
              CURLOPT_RETURNTRANSFER => 1,
              CURLOPT_URL => $queryUrl,
              CURLOPT_POSTFIELDS => $queryData,
            ));
            $result = curl_exec($curl);
            curl_close($curl);
            $result = json_decode($result, 1);
            if (is_array($result) && array_key_exists('error', $result)) {
                $this->log('Bitrix24: ' . ($result['error_description'] ?? $result['error']), 'error');
            }
            if( $this->$model->save($entity) && is_array($result) && !empty($result['result']) ){
				$message = '<p><b>Имя:</b> ' . $data['name'] . '</p><p><b>Телефон: </b>' . $data['phone'] .'</p>';
				$mailer = new Mailer("smtp");
				$mailer->viewBuilder()->setTemplate('default','default');
				$mailer
					->setEmailFormat('html')
					->setFrom(['st-kotel.kz@yandex.ru' => 'mzhk-astana.kz'])
					->setTo('pangaea2020@mail.ru')
					// ->addTo('zhasylzher01@gmail.com')
					->setSubject('Заявка с сайта mzhk-astana.kz')
					->setViewVars(['content' => $message])
					->send();
                return $this->__respond(true, __('Ваша заявка принята'));
            } else{
                return $this->__respond(false, __('Ошибка отправки'));
            }
			}
		    //}else {
           //  $this->Flash->error(__('Вы не прошли проверку'));
           //  return $this->redirect( $this->referer() );
		//}
        
    }

	private function __checkRecaptchaResponse($response){
        // verifying the response is done through a request to this URL
        $url = 'https://www.google.com/recaptcha/api/siteverify';
        // The API request has three parameters (last one is optional)
        $data = array('secret' => Configure::read('Recaptcha.SecretKey'),
            'response' => $response,
            'remoteip' => $_SERVER['REMOTE_ADDR']);

        // use key 'http' even if you send the request to https://...
        $options = array(
            'http' => array(
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($data),
            ),
        );

        // We could also use curl to send the API request
        $context  = stream_context_create($options);
        $json_result = file_get_contents($url, false, $context);
        $result = json_decode($json_result);
        return $result->success;
    }
}


 ?>