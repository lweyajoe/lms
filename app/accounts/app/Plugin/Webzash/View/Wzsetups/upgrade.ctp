<?php
/**
 * The MIT License (MIT)
 *
 * Webzash - Easy to use web based double entry accounting software
 *
 * Copyright (c) 2014 Prashant Shah <pshah.mumbai@gmail.com>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */
?>
<script type="text/javascript">
$(document).ready(function() {
	$("#WzsetupDbDatasource").change(function() {
		if ($(this).val() == "Database/Postgres") {
			$("#WzsetupDbSchema").parent().show();
		} else {
			$("#WzsetupDbSchema").parent().hide();
			$("#WzsetupDbSchema").val("");
		}
	});
	$('#WzsetupDbDatasource').trigger('change');
});
</script>

<div>
	<?php echo '<div id="page-title-second">' . __d('webzash', 'Please enter new MySQL database (should be empty) details for importing old application master data. Please note this will "NOT" affect any of the ACCOUNT data which was stored in MySQL.') . '</div>'; ?>
</div>

<div class="wzinstall upgrade form">
	<?php
		echo $this->Form->create('Wzsetup', array(
			'type' => 'file',
			'inputDefaults' => array(
				'div' => 'form-group',
				'wrapInput' => false,
				'class' => 'form-control',
			),
		));

		echo $this->Form->input('db_datasource', array('type' => 'select', 'options' => $this->Generic->wzaccount_dbtype_options(), 'label' => __d('webzash', 'Database type')));
		echo $this->Form->input('db_database', array('label' => __d('webzash', 'Database name')));
		echo $this->Form->input('db_schema', array(
			'label' => __d('webzash', 'Database schema'),
			'afterInput' => '<span class="help-block">' . __d('webzash', 'Note : Database schema is required for Postgres database connection. Leave it blank for MySQL connections.') . '</span>',
		));
		echo $this->Form->input('db_host', array('label' => __d('webzash', 'Database host')));
		echo $this->Form->input('db_port', array('label' => __d('webzash', 'Database port')));
		echo $this->Form->input('db_login', array('label' => __d('webzash', 'Database login')));
		echo $this->Form->input('db_password', array('type' => 'password', 'label' => __d('webzash', 'Database password')));
		echo $this->Form->input('db_prefix', array(
			'label' => __d('webzash', 'Database table prefix'),
			'afterInput' => '<span class="help-block">' . __d('webzash', 'Note : Database table prefix to use (optional). All tables for this account will be created with this prefix, useful if you have only one database available and want to use multiple accounts. Prefix should not begin in digits eg: 24 - this might crash the code -  ... but am24 is acceptable. ') . '</span>',
		));
		echo $this->Form->input('db_persistent', array('type' => 'checkbox', 'label' => __d('webzash', 'Use persistent connection'), 'class' => 'checkbox'));

		echo $this->Form->input('old_sqlite', array(
			'type' => 'file',
			'label' => '<span class="alert-text">' . __d('webzash', 'UPLOAD OLD VERSION 2.x MASTER DATA FILE named "webzash.sqlite". DEFAULT LOCATION OF THE FILE WAS "app/Plugin/Webzash/Database/webzash.sqlite"') . '</span>',
		));

		echo $this->Form->submit(__d('webzash', 'Upgrade'), array(
			'div' => false,
			'class' => 'btn btn-primary'
		));

		echo $this->Html->tag('span', '', array('class' => 'link-pad'));
		echo $this->Html->link(__d('webzash', 'Cancel'), array('plugin' => 'webzash', 'controller' => 'wzsetups', 'action' => 'index'), array('class' => 'btn btn-default'));
		echo '</div>';

		echo $this->Form->end();
	?>
</div>
