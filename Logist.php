<?php

use Krugozor\Database\Mysql\Mysql as Mysql;

class Model_DB_Logist
{

    protected $db;

    protected $error;

    public function __construct($server, $username, $password, $port=null, $socket=null, $db_name, $charset=null, $timezone)
    {
        try {
            $this->db = Mysql::create($server, $username, $password)
                ->setCharset($charset)
                ->setDatabaseName($db_name);
            $this->db->query("SET NAMES '?s', time_zone = '?s'", $charset, $timezone);
            $this->error = false;
        } catch (Exception $exception) {
            $this->error = true;
        }
    }

    public function is_error()
    {
        return $this->error;
    }


    // Insert new reis
    public function reis_insert($id, $number, $napravlenie_id, $status_id, $client_type, $client_id, $car_id, $date_otpr, $date_prib, $count_orders, $sum_ves, $sum_obem, $sum_prihod, $sum_rashod_plan, $sum_rashod_fact, $money_predopl, $date_predopl_plan, $date_predopl_fact, $money_ostatok, $date_ostatok_plan, $date_ostatok_fact, $user_id, $orders)
    {
        $result = array('id' => 0, 'error' => 0, 'error_text' => '');
        try {
            $this->db->query("SET AUTOCOMMIT=0;");
            $this->db->query("BEGIN;");
            $query = "INSERT INTO `reis` (`id`, `create_date`, `number`, `napravlenie_id`, `status_id`, `client_type`, `client_id`, `car_id`, `date_otpr`, `date_prib`, `count_orders`, `sum_ves`, `sum_obem`, `sum_prihod`, `sum_rashod_plan`, `sum_rashod_fact`, `money_predopl`, `date_predopl_plan`, `date_predopl_fact`, `money_ostatok`, `date_ostatok_plan`, `date_ostatok_fact`, `user_id`) VALUES (NULL, CURRENT_DATE(), '?s', ?i, ?i, ?i, ?i, ?i, '?s', '?s', ?i, ?d, ?d, ?d, ?d, ?d, ?d, '?s', '?s', ?d, '?s', '?s', ?i);";
            $this->db->query($query, $number, $napravlenie_id, $status_id, $client_type, $client_id, $car_id, $date_otpr, $date_prib, $count_orders, $sum_ves, $sum_obem, $sum_prihod, $sum_rashod_plan, $sum_rashod_fact, $money_predopl, $date_predopl_plan, $date_predopl_fact, $money_ostatok, $date_ostatok_plan, $date_ostatok_fact, $user_id);
            $result['id'] = $this->db->getLastInsertId();
            foreach ($orders as $order_id) {
                $order_status = $this->order_get_status($order_id);
                if ($order_status === FALSE) {
                    throw new Exception('Error order not found');
                } else {
                    if ($order_status['is_deleted'] == 1) {
                        throw new Exception('Error order deleted');
                    } elseif ($order_status['reis_id'] > 0) {
                        throw new Exception('Error order on another reis');
                    } else {
                        $this->order_set_reis($order_id, $result['id']);
                    }
                }
            }
            $this->db->query("COMMIT;");
        } catch (Exception $e) {
            $this->db->query("ROLLBACK;");
            $result['error'] = 1;
            $result['error_text'] = $e->getMessage();
        }
        return $result;
    }


    // Get order status
    public function order_get_status($id)
    {
        try {
            $query = "SELECT `id`, `status_id`, `reis_id`, `zabor_id`, `dostavka_id`, `is_deleted` FROM `orders` WHERE `id`=?i;";
            $streets = $this->db->query($query, $id);
            $result = $streets->fetch_assoc();
            if (!isset($result)) {$result = false;}
        } catch (Exception $e) {
            $result = false;
        }
        return $result;
    }


    // Order set reis
    public function order_set_reis($id, $reis_id) {
        $result = array('id' => 0, 'error' => 0);
        try {
            $query = "UPDATE `orders` SET `reis_id` = ?i WHERE `orders`.`id` = ?i;";
            $this->db->query($query, $reis_id, $id);
            $result['id'] = $id;
        } catch (Exception $e) {
            $result['error'] = 1;
        }
        return $result;
    }


    // Get settings
    public function settings_get($type)
    {
        $result = array('data' => [], 'error' => 0, 'error_text' => '');
        try {
            $query = "SELECT `name`, `value` FROM `settings` WHERE `name` LIKE '?s%';";
            $streets = $this->db->query($query, $type);
            $status = $streets->fetch_assoc_array();
            if (!isset($status)) {
                $result['error'] = 1;
            } else {
                foreach ($status as $param) {
                    $result['data'][$param['name']] = $param['value'];
                }
            }
        } catch (Exception $e) {
            $result['error'] = 1;
            $result['error_text'] = $e->getMessage();
         }
        return $result;
    }


    // Set settings
    public function settings_set($params) {
        $result = array('error' => 0, 'error_text' => '');
        try {
            $this->db->query("SET AUTOCOMMIT=0;");
            $this->db->query("BEGIN;");
            foreach ($params as $key => $value) {
                $query = "UPDATE `settings` SET `value` = '?s' WHERE `name`='?s';";
                $this->db->query($query, $value, $key);
            }
            $this->db->query("COMMIT;");
        } catch (Exception $e) {
            $this->db->query("ROLLBACK;");
            $result['error'] = 1;
            $result['error_text'] = $e->getMessage();
        }
        return $result;
    }


    // Get manager orders
    public function report_manager_orders($date1, $date2)
    {
        $percents = $this->settings_get('report_manager');
        try {
            $query = "SELECT `id`, `name`, (SELECT COUNT(*) FROM `orders` WHERE `orders`.`user_id`=`users`.`id` AND `is_deleted` = 0 AND `date`>='?s' AND `date`<='?s') AS `orders_count`, @plan:=(SELECT SUM(`fraht_prihod_plan`) FROM `orders` WHERE `orders`.`user_id`=`users`.`id` AND `is_deleted` = 0 AND `date`>='?s' AND `date`<='?s') AS `prihod_plan`, @fact:=(SELECT SUM(`fraht_prihod_fact`) FROM `orders` WHERE `orders`.`user_id`=`users`.`id` AND `is_deleted` = 0 AND `date`>='?s' AND `date`<='?s') AS `prihod_fact`, ROUND(@plan / 100 * ?d) AS `percent_plan`, ROUND(@fact / 100 * ?d) AS `percent_fact` FROM `users` WHERE `users`.`is_deleted`=0;";
            $streets = $this->db->query($query, $date1, $date2, $date1, $date2, $date1, $date2, $percents['data']['report_manager_percent_plan'], $percents['data']['report_manager_percent_fact']);
            $result = $streets->fetch_assoc_array();
            if (!isset($result)) {$result = false;}
        } catch (Exception $e) {
            $result = false;
        }
        return $result;
    }


