<?php

require_once 'AppController.php';
require_once __DIR__ .'/../models/Item.php';
require_once __DIR__.'/../repository/TransportRepository.php';

class TransportController extends AppController {

    const MAX_FILE_SIZE = 1024*1024;
    const SUPPORTED_TYPES = ['image/png', 'image/jpeg'];
    const UPLOAD_DIRECTORY = '/../public/uploads/';

    private $message = [];
    private $transportRepository;

    public function __construct()
    {
        parent::__construct();
        $this->transportRepository = new TransportRepository();
    }

    public function addTransportNotice()
    {
        if ($this->isPost() && is_uploaded_file($_FILES['file']['tmp_name']) && $this->validate($_FILES['file'])) {
            move_uploaded_file(
                $_FILES['file']['tmp_name'],
                dirname(__DIR__).self::UPLOAD_DIRECTORY.$_FILES['file']['name']
            );

            // TODO create new project object and save it in database
            $item = new Item(
                $_POST['startCity'],
                $_POST['startStreet'],
                $_POST['startNumber'],
                $_POST['endCity'],
                $_POST['endStreet'],
                $_POST['endNumber'],
                $_POST['width'],
                $_POST['height'],
                $_POST['depth'],
                $_POST['name'],
                $_POST['type'],
                $_POST['payment'],
                $_POST['time'],
                $_POST['passengers'],
                $_FILES['file']['name'],
                $_POST['description']);

            $this->transportRepository->addTransportNotice($item);

            return $this->render('transport_notice', [
                'transport_notice' => $this->transportRepository->getTransportNotices(),
                'messages' => $this->message
            ]);
        }
        return $this->render('add_transport_notice', ['messages' => $this->message]);
    }

    private function validate(array $file): bool
    {
        if ($file['size'] > self::MAX_FILE_SIZE) {
            $this->message[] = 'File is too large for destination file system.';
            return false;
        }

        if (!isset($file['type']) || !in_array($file['type'], self::SUPPORTED_TYPES)) {
            $this->message[] = 'File type is not supported.';
            return false;
        }
        return true;
    }

}