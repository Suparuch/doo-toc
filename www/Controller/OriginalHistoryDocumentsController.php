<?php

App::uses('AppController', 'Controller');

class OriginalHistoryDocumentsController extends AppController {

    public $name = 'OriginalHistoryDocuments';
    public $uses = array('OriginalHistoryDocument', 'HMUnit', 'TableAttachFile');
    public $components = array("FileStorageComponent", "Generator", "TextUtil");

    function beforeFilter() {
        $currentUser = $this->Session->check('AuthUser');
        if (empty($currentUser)) {
            $this->Session->delete('AuthUser');
            $this->Session->destroy();

            $url = Configure::read('Application.Domain') . 'Logins';
            $this->redirect($url);
        }
    }

    public function index() {
        Configure::write('debug', 2);
        //$this->layout = 'blank';
        //$this->autoRender = false;

        $dataRequest = $this->request->data;
        if (empty($dataRequest)) {
            $offset = 0;
            $whereConditions = array();
            $whereConditions['deleted'] = 'N';
            $whereConditions[] = "OriginalHistoryDocument.secret = '0'";
            $conditions = array('limit' => $this->Generator->setLimit(),
                'order' => array('id' => 'desc'),
                'conditions' => $whereConditions
            );
            $total = $this->OriginalHistoryDocument->find('count', $conditions);
            $OriginalHistoryDocuments = $this->OriginalHistoryDocument->find('all', $conditions);
        } else {

            $default = array();
            $default = $dataRequest;
            //$this->set('default', $default);
            // intitial
            $whereConditions = array();
            $whereConditions['deleted'] = 'N';

            $offset = $dataRequest['offset'];

            // Search condition
            // 
            $secret = $dataRequest['rdbSecret'];
            $whereConditions[] = "OriginalHistoryDocument.secret = '" . $secret . "'";
            if (!empty($dataRequest['txtOhdTitle'])) {
                $whereConditions[] = '(OriginalHistoryDocument.ohd_title like \'%' . trim($dataRequest['txtOhdTitle']) . '%\' )';
            }
            if (!empty($dataRequest['ddlUnit'])) {
                $whereConditions[] = '(OriginalHistoryDocument.ohd_div_id = \'' . trim($dataRequest['ddlUnit']) . '\' )';
            }
            if (!empty($dataRequest['txtOhdDocNo'])) {
                $whereConditions[] = '(OriginalHistoryDocument.ohd_doc_no like \'%' . trim($dataRequest['txtOhdDocNo']) . '%\' )';
            }
            if (!empty($dataRequest['dpkOhdDocDate'])) {
//                $dStart = $this->TextUtil->formatPckDateForWhereCond($dataRequest['dpkOhdDocDate']); //sprintf("%1$04d-%2$02d-%3$02d", $start_date['year'], $start_date['month'], $start_date['day']);
//                $whereConditions[] = '(OriginalHistoryDocument.ohd_doc_date =\'' . $dStart . '\' )';
                $dStart = $this->TextUtil->formatDateForWhereCond($dataRequest['dpkOhdDocDate']);
                if (!empty($dStart)) {
                    $whereConditions[] = '(OriginalHistoryDocument.ohd_doc_date =\'' . $dStart . '\' )';
                } else {
                    $default['dpkOhdDocDate']['day'] = '';
                    $default['dpkOhdDocDate']['month'] = '';
                    $default['dpkOhdDocDate']['year'] = '';
                }
            }
            if (!empty($dataRequest['txtOhdSender'])) {
                $whereConditions[] = '(OriginalHistoryDocument.ohd_sender like \'%' . trim($dataRequest['txtOhdSender']) . '%\' )';
            }
            if (!empty($dataRequest['txtOhdReceiver'])) {
                $whereConditions[] = '(OriginalHistoryDocument.ohd_receiver like \'%' . trim($dataRequest['txtOhdReceiver']) . '%\' )';
            }
            if (!empty($dataRequest['txtOhdClosetNo'])) {
                $whereConditions[] = '(OriginalHistoryDocument.ohd_closet_no like \'%' . trim($dataRequest['txtOhdClosetNo']) . '%\' )';
            }
            if (!empty($dataRequest['txtOhdOtherNo'])) {
                $whereConditions[] = '(OriginalHistoryDocument.ohd_other_no like \'%' . trim($dataRequest['txtOhdOtherNo']) . '%\' )';
            }

            $conditions = array('limit' => $this->Generator->setLimit(), 'offset' => $offset,
                'order' => array('id' => 'desc'),
                'conditions' => $whereConditions
            );

            $this->set('default', $default);

            // Query
            $total = $this->OriginalHistoryDocument->find('count', array('conditions' => $whereConditions));
            $OriginalHistoryDocuments = $this->OriginalHistoryDocument->find('all', $conditions);
        }

        // Set breadcrumb
        $breadcrumb = array(
            'controller' => array(
                'name' => 'ระบบงานประวัติศาสตร์และพิพิธภัณฑ์ทหาร',
                'url' => '',
                'subtitle' => 'ต้นฉบับเอกสารประวัติศาสตร์'
            ),
        );
        $this->set('breadcrumb', $breadcrumb);

        $conditions = array();
        $conditions = array('conditions' => array('deleted' => 'N'), 'order' => array('order_sort' => 'asc'), 'fields' => array('id', 'name'), 'cache' => 'longNameList', 'cacheConfig' => 'long');
        $Units = $this->HMUnit->find('list', $conditions);

        // Set model to view
        $this->set('Units', $Units);
        $this->set('OriginalHistoryDocuments', $OriginalHistoryDocuments);
        $pagination = array('offset' => $offset, 'total' => $total, 'limit' => $this->Generator->setLimit(), 'model' => 'OriginalHistoryDocument', 'pages' => 5);
        $this->set('pagination', $pagination);
    }

    public function form($id = null) {
        Configure::write('debug', 2);

        $this->layout = 'blank';
        $default = array();
        $default['id'] = $this->Generator->getID();

        if (!empty($id)) {
            $whereConditions = array();
            $whereCOnditions['deleted'] = 'N';
            $whereConditions['id'] = $id;
            $conditions = array('limit' => '1',
                'order' => array('id' => 'asc'),
                'conditions' => $whereConditions
            );
            $OriginalHistoryDocuments = $this->OriginalHistoryDocument->find('all', $conditions);

            foreach ($OriginalHistoryDocuments[0]['OriginalHistoryDocument'] as $key => $value)
                $default[$key] = $value;

            //$this->set('default', $default);
            $default['action'] = 'EDIT';
        } else {
            $default['action'] = 'ADD';
        }
        $this->set('default', $default);

        $whereConditions = array();
        $whereConditions['deleted'] = 'N';
        $whereConditions['file_table_name'] = 'OriginalHistoryDocuments';
        $whereConditions['file_table_key'] = $default['id'];
        $conditions = array('limit' => $this->Generator->setLimit(),
            'order' => array('id' => 'asc'),
            'conditions' => $whereConditions
        );
        $TableAttachFiles = $this->TableAttachFile->find('all', $conditions);

        $conditions = array();
        $conditions = array('conditions' => array('deleted' => 'N'), 'order' => array('order_sort' => 'asc'), 'fields' => array('id', 'name'), 'cache' => 'longNameList', 'cacheConfig' => 'long');
        $Units = $this->HMUnit->find('list', $conditions);

        // Set model to view
        $this->set('Units', $Units);
        $this->set('TableAttachFiles', $TableAttachFiles);
    }

    public function save($id = null, $formAction = "EDIT") {
        Configure::write('debug', 2);
        $this->autoRender = false;
        $data = $this->request->data;

        if (!empty($data)) {
            if (!empty($id)) {
                $this->OriginalHistoryDocument->id = $id;
                if ($formAction == "ADD") {
                    $data['id'] = $id;
                    $this->OriginalHistoryDocument->create();
                }
                $status = $this->OriginalHistoryDocument->save($data) ? 'SUCCESS' : 'FAILED';
            }
        } else
            $status = 'FAILED';

        echo json_encode(array('status' => $status));
    }

    public function delete() {
        $this->autoRender = false;
        $status = '';

        $ids = array();
        $ids = $this->request->data['ids'];

        foreach ($ids as $id) {
            if (!empty($id)) {
                $data = array();
                $data['deleted'] = "Y";
                $this->OriginalHistoryDocument->id = $id;
                if ($this->OriginalHistoryDocument->exists())
                    $status = $this->OriginalHistoryDocument->save($data) ? 'SUCCESS' : 'FAILED';
                else
                    $status = 'FAILED';
            } else
                $status = 'FAILED';
        }

        echo json_encode(array('status' => $status));
    }

    public function uploadDocument($pk_id = null) {
        $this->layout = 'blank';
        $this->autoRender = false;

        $response = array();
        if (!empty($_FILES)) {
            $original_name = $_FILES['Filedata']['name'];
            $file_type = $_FILES['Filedata']['type'];
            $original_size = $_FILES['Filedata']['size'];
            $code = String::uuid();
            $allowedExts = array('docx', 'txt', 'pdf', 'jpg', 'jpeg', 'png', 'gif', 'xlsx', 'pptx', 'bmp', 'zip', 'swf');
            $receieve_error = explode(".", $original_name);
            $extension = strtolower(end($receieve_error));

            // Check file size (not over 5MB)
            if ($original_size <= 1048576) {
                $extensionAllowUpload = in_array($extension, $allowedExts);
                if ($extensionAllowUpload) {
                    if (is_uploaded_file($_FILES['Filedata']['tmp_name'])) {
                        if ($_FILES['Filedata']['error'] > 0) {
                            echo __("Return Error Code: ") . $_FILES['Filedata']['error'] . "<br>";
                        } else {
                            $path = 'files/originalhistorydocuments/';
                            if (!file_exists($path)) {
                                mkdir($path, 0777, true);
                            }
                            $uploadSuccess = move_uploaded_file($_FILES['Filedata']['tmp_name'], $path . $code . '.' . $extension);
                            $filename = $code . '.' . $extension;
                            if ($uploadSuccess) {
                                $key = $code;

                                // Save file data to table
                                $document = array();

                                $created = date('Y-m-d H:i:s');
                                $updated = date('Y-m-d H:i:s');
                                $created_by = 2; //$currentUser['AuthUser']['id'];
                                $updated_by = 2; //$currentUser['AuthUser']['id'];

                                $document_id = $this->Generator->getID();
                                $document['id'] = $document_id;

                                $document['file_table_name'] = 'OriginalHistoryDocuments';
                                $document['file_table_key'] = $pk_id;

                                $document['file_name'] = $filename;
                                $document['file_type'] = $file_type;
                                $document['file_size'] = $original_size;
                                $document['file_key'] = $key;
                                $document['file_path'] = $path;
                                $document['file_original_name'] = $original_name;

                                $document['created'] = $created;
                                $document['updated'] = $updated;
                                $document['deleted'] = 'N';
                                $document['created_by'] = $created_by;
                                $document['updated_by'] = $updated_by;

                                $this->TableAttachFile->create();
                                $this->TableAttachFile->save($document);

                                $response[] = array('result' => 'success', 'document_id' => $document_id, 'document_original_name' => $document['file_original_name'], 'document_name' => $filename, 'key' => $key);
                            } else {
                                echo '-1';
                                $result = false;
                            }
                        }
                    }
                } else {
                    $result = false;
                    $response[] = array('result' => 'error', 'detail' => 'ไม่รองรับไฟล์นามสกุล .' . $extension);
                }
            } else {
                $result = false;
                $response[] = array('result' => 'error', 'detail' => 'ขนาดไฟล์ Upload เกิน 5 Mb');
            }
        }

        echo json_encode($response);
    }

    public function removeDocument($doc_id) {
        $this->layout = 'blank';
        $this->autoRender = false;
        $this->disableCache();
        $data = $this->request->data;

        if (!empty($data)) {

            $data = array();
            $data['TableAttachFile']['id'] = $doc_id;
            $data['TableAttachFile']['deleted'] = 'Y';

            $status = $this->TableAttachFile->save($data) ? 'SUCCESS' : 'FAILED';

            echo json_encode(array('status' => $status, 'id' => $doc_id, 'message' => 'ลบเอกสารเรียบร้อย'));
        }
    }

}

?>