<?phpclass m130614_094712_create_serv_girl_table extends CDbMigration{	public function up()	{        $this->createTable(            'serv_girl', array(                'g_id' => 'int(11) NOT NULL',                's_id' => 'int(11) NOT NULL',                "PRIMARY KEY (`g_id`, `s_id`)",            )        );        $this->addForeignKey(            'fk_girl_service',            'serv_girl', 'g_id',            'girl', 'g_id',            'CASCADE',            'CASCADE');        $this->addForeignKey(            'fk_service_girl',            'serv_girl', 's_id',            'service', 's_id',            'CASCADE',            'CASCADE');	}	public function down()	{        $this->dropForeignKey('fk_service_girl', 'serv_girl');        $this->dropForeignKey('fk_girl_service', 'serv_girl');		$this->dropTable('serv_girl');	}}