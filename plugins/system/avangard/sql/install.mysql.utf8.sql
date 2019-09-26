CREATE TABLE IF NOT EXISTS `#__jshopping_avangard` (
	id int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
	order_id int(11) UNSIGNED DEFAULT NULL,
	    ticket_id varchar(40) DEFAULT NULL,
	    avangard_id  int(10) DEFAULT NULL,
	    ok_code varchar(10) DEFAULT NULL,
	    failure_code varchar(10) DEFAULT NULL,
	    response_code int(10) DEFAULT NULL,
		response_message text,
		index (order_id)
) ENGINE = MYISAM ;
