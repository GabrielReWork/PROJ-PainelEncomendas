<?php
class ModelFretespainelEncomendas extends Model {

    function getEncomendas($filter_data = []) {
        $sql = "SELECT e.cod_transportadora, e.num_nota, e.prev_delivery, e.status, c.firstname, c2.cpf_cnpj
                FROM esp_etiqueta_transporte e
                LEFT JOIN erp_customer c ON e.cod_transportadora = c.customer_id
                LEFT JOIN erp_customer c2 ON e.cod_cliente = c2.customer_id
                WHERE 1";

        if (!empty($filter_data['nfe'])) {
            $sql .= " AND e.num_nota LIKE '%" . $this->db->escape($filter_data['nfe']) . "%'";
        }
        if (!empty($filter_data['status'])) {
            $sql .= " AND e.status = '" . (int)$filter_data['status'] . "'";
        }
        if (!empty($filter_data['transportadora'])) {
            $sql .= " AND c.firstname LIKE '%" . $this->db->escape($filter_data['transportadora']) . "%'";
        }
        if (!empty($filter_data['cpf_cliente'])) {
            $sql .= " AND c2.cpf_cnpj LIKE '%" . $this->db->escape($filter_data['cpf_cliente']) . "%'";
        }
        
        $sql .= " ORDER BY e.num_nota DESC LIMIT 100";

        $query = $this->db->query($sql);
        
        if ($query) {
            return $query->rows;
        } else {
            return [];
        }
    }


    function buscarTransportadoras($firstname) {
        $sql = "SELECT DISTINCT customer_id, firstname FROM erp_customer WHERE customer_group_id = 4 AND firstname LIKE '%" . $this->db->escape($firstname) . "%' LIMIT 20";
        $query = $this->db->query($sql);
        return $query->rows;
    }

}
?>