<?php
/**
 * @author Marten Koedam
 * @package Sheetgen
 * @subpackage Diceroller
 * @since 06-feb-2010
 * @license www.dalines.org/license
 * @copyright 2012, Dalines Sofware Library
 */

class WikiSheetStatistics extends WikiPotion {
	
	function init() {
		
		$conf = VoodooIni::load('sheetgen');
		$this->_map = $conf['sheets'];
			
		$sql = "SELECT COUNT(SHEET_ID) as cnt FROM TBL_SHEET_USER";
		$q = $this->formatter->db->query($sql);
		$q->execute();
		
		$r = $q->fetch();
		$total = $r->cnt;
		
		
		$sql = "SELECT SHEET_TYPE as Game, COUNT(SHEET_ID) as `Absolute`, COUNT(SHEET_ID) as `Share`, ".$total." as Total FROM TBL_SHEET_USER GROUP BY SHEET_TYPE ORDER BY `Share` DESC";
		$q = $this->formatter->db->query($sql);
		$q->execute();

		require_once(CLASSES.'TableFactory.php');
		$tf = new TableFactory($q);
		$tf->setHiddenField('Total');
		$tf->setValueProcessor(array('Game', 'Share'), array($this, 'tfValueProcessor'));
		$this->display = $tf->getXHTMLTable('list report', substr(md5('WikiMySheets'),0,5));	
	}
	
	function tfValueProcessor($args) {
		
		switch($args['head']) {
			
			case 'Share':
				return number_format(($args['value']/$args['row']['Total'])*100, 2).'%';
			break;
			case 'Game':
				
				return $this->_map[$args['value']];
			break;
		}
		
		return $args['value'];
		
	}
	
}
 
?>