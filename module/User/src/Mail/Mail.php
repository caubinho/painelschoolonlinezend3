<?php

namespace User\Mail;

use Zend\View\Model\ViewModel;
use Zend\View\Renderer\RendererInterface;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mail
{

    /**
     * @var RendererInterface
     */
    private $renderer;
    private $view;
    private $entityManager;

    public function __construct($entityManager, $renderer, $view)
    {

        $this->renderer = $renderer;
        $this->view = $view;
        $this->entityManager = $entityManager;
    }


    public function sendEmail($name, $subject, $destine, $link, $body, $pagina, $file, $config)
    {

        /** @var \User\Entity\Setup  $data */
        foreach ($config as $u => $data){}

        $nome = $name;



        /** config de envio */
        $quemEnvia      = $data->getEmail();
        $destinatario   = $destine;
        $assunto        = $subject;
        $corpoMSG       = $this->renderView($pagina , [ 'body' => $body, 'link' => $link]);


        // Inicia a classe PHPMailer
        $mail = new PHPMailer(true);


        try{

        $mail->CharSet = "UTF-8";
        $mail->setLanguage('br', $_SERVER['DOCUMENT_ROOT'] . 'public/email/language/');

        $mail->SMTPDebug = $data->getDebug();

        // Define os dados do servidor e tipo de conexão
        // =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
        $mail->IsSMTP(); // Define que a mensagem será SMTP


            //Server settings
            $mail->isSMTP();          // Set mailer to use SMTP

            $mail->CharSet = "UTF-8";
            $mail->setLanguage('br', 'public/email/language/');

            if($data->getDebug() === "0"){
            }else {
                $mail->SMTPSecure = $data->getDebug();
            }

            $mail->Host     =  $data->getHost(); // Endereço do servidor SMTP (Autenticação, utilize o host smtp.seudomínio.com.br)
            $mail->SMTPAuth =   true;  // Usar autenticação SMTP (obrigatório para smtp.seudomínio.com.br)
            $mail->SMTPAutoTLS = false;
            $mail->Port     =  $data->getPort(); //  Usar 587 porta SMTP
            $mail->Username =  $data->getEmailhost(); // Usuário do servidor SMTP (endereço de email)
            $mail->Password =  $data->getPasshost(); // Senha do servidor SMTP (senha do email usado)

            //Define o remetente
            // =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
            $mail->setFrom($quemEnvia, 'AVA-SPP'); //Seu e-mail
            //$mail->AddReplyTo($quemEnvia, 'AVA-SPP'); //Seu e-mail
            $mail->Subject =  $assunto;//Assunto do e-mail

            //Define os destinatário(s)
            //=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
            $mail->addAddress($destinatario, $nome);

            $mail->isHTML(true);
            //Define o corpo do email
            $mail->msgHTML( $corpoMSG );


            if(empty($file)){

            }else{

                $mail->addAttachment($file);
            }


           $mail->Send();

            return true;


        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$e->errorMessage()}";

            return false;
        }




    }

    public function renderView($page, array $data)
    {
        $model = new ViewModel();
        $model->setTemplate("mailer/{$page}.phtml");
        $model->setOption('has_parent',true);
        $model->setVariables($data);

        return $this->view->render($model);
    }



}
