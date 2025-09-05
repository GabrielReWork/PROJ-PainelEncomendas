<?php
class ControllerFretespainelEncomendas extends Controller {

    public function index() {
        $this->load->model('fretes/painelEncomendas');

        $data['user_token'] = $this->session->data['user_token'];
        $data['header']      = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer']      = $this->load->controller('common/footer');

        // Adiciona as opções de status ao template
        $data['statuss'] = $this->getStatusOptions();

        // Obtém e formata os dados para a carga inicial
        $entregas_raw = $this->model_fretes_painelEncomendas->getEncomendas();
        $entregas_formatted = $this->applyTransportadoraNames($entregas_raw);
        $data['entregas'] = $this->applyStatusNames($entregas_formatted);

        $this->response->setOutput($this->load->view('fretes/painelEncomendas', $data));
    }

    public function filter() {
        $this->load->model('fretes/painelEncomendas');

        $filter_data = [
            'nfe'             => isset($this->request->post['nfe']) ? $this->request->post['nfe'] : '',
            'status'          => isset($this->request->post['status']) ? $this->request->post['status'] : '',
            'transportadora'  => isset($this->request->post['transportadora']) ? $this->request->post['transportadora'] : '',
            'cpf_cliente'     => isset($this->request->post['cpf_cliente']) ? $this->request->post['cpf_cliente'] : ''
        ];

        $encomendas_raw = $this->model_fretes_painelEncomendas->getEncomendas($filter_data);
        $encomendas_formatted = $this->applyTransportadoraNames($encomendas_raw);
        $encomendas = $this->applyStatusNames($encomendas_formatted);



        // echo '<pre>';
        // print_r($encomendas);
        // echo '</pre>';
        // exit;

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($encomendas));
    }

    private function getStatusMap() {
        return array(
            1 => 'AGUARDANDO COLETA',
            2 => 'EM TRANSITO',
            3 => 'CANCELADA',
            4 => 'ENTREGUE',
            5 => 'FALHA NA ENTREGA',
            6 => 'AGUARDANDO RETIRADA',
            7 => 'DEVOLUÇÃO',
            8 => 'EXTRAVIO',
            9 => 'DANIFICADO',
            10 => 'FURTO/ROUBO'
        );
    }
    
    public function getStatusOptions() {
        $statusMap = $this->getStatusMap();
        $options = [];
        foreach ($statusMap as $id => $name) {
            $options[] = ['id' => $id, 'name' => $name];
        }
        return $options;
    }

    public function applyStatusNames($encomendas) {
        $statusMap = $this->getStatusMap();
        foreach ($encomendas as &$encomenda) {
            $encomenda['status_code'] = $encomenda['status']; // Guarda o código original para o JS
            $statusId = $encomenda['status'];
            if (isset($statusMap[$statusId])) {
                $encomenda['status'] = $statusMap[$statusId];
            }
        }
        return $encomendas;
    }

    public function applyTransportadoraNames($encomendas) {
        foreach ($encomendas as &$encomenda) {
            switch ($encomenda['firstname']) {
                case 'AGENCIA DE CORREIOS FRANQUEADA SANTA CRUZ LTDA':
                    $encomenda['firstname'] = 'CORREIOS SANTA CRUZ LTDA';
                    break;
                case 'TNT MERCURIO CARGAS E ENCOMENDAS EXPRESSAS LTDA.':
                    $encomenda['firstname'] = 'TNT LTDA';
                    break;
                case 'BRASPRESS TRANSPORTES URGENTES LTDA':
                    $encomenda['firstname'] = 'BRASPRESS LTDA';
                    break;
                case 'TRANS FARMA LOGISTICA PARA SAUDE LTDA':
                    $encomenda['firstname'] = 'TRANS FARMA LTDA';
                    break;
                case 'QUALITY TRANSPORTES E ENTREGAS RAPIDAS LTDA':
                    $encomenda['firstname'] = 'QUALITY LTDA';
                    break;
                case 'SHOPEE EXPRESS INTERNATIONAL II PRIVATE LIMITED':
                    $encomenda['firstname'] = 'SHOPEE EXPRESS';
                    break;
                case 'RODONAVES TRANSPORTES E ENCOMENDAS LTDA':
                    $encomenda['firstname'] = 'RODONAVES LTDA';
                    break;
            }
        }
        return $encomendas;
    }

    public function autocomplete() {
        $this->load->model('fretes/painelEncomendas');

        // Mudar de $this->request->get para $this->request->post
        $firstname = isset($this->request->post['firstname']) ? $this->request->post['firstname'] : '';

        $results = $this->model_fretes_painelEncomendas->buscarTransportadoras($firstname);

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($results));
    }



}
?>