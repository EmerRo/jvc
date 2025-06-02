<?php


class TableData
{
	private $_db;
	public function __construct()
	{
		try {
			$host = HOST_SS;
			$database = DATABASE_SS;
			$user = USER_SS;
			$passwd = PASSWORD_SS;

			$this->_db = new PDO('mysql:host=' . $host . ';dbname=' . $database, $user, $passwd, array(
				PDO::ATTR_PERSISTENT => true,
				PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
			));

		} catch (PDOException $e) {
			error_log("Failed to connect to database: " . $e->getMessage());
		}
	}

	public function getAlmacen($table, $index_column, $columns, $isExtra = false, $orderBy = "", $where = false)
	{
		// Paging
		$sLimit = "";
		if (isset($_GET['iDisplayStart']) && $_GET['iDisplayLength'] != '-1') {
			$sLimit = "LIMIT " . intval($_GET['iDisplayStart']) . ", " . intval($_GET['iDisplayLength']);
		}

		// Ordering
		$sOrder = "";
		if (!empty($orderBy)) {
			// Si se proporciona un orderBy personalizado, úsalo
			$sOrder = $orderBy;
		} else if (isset($_GET['iSortCol_0'])) {
			$sOrder = "ORDER BY  ";
			for ($i = 0; $i < intval($_GET['iSortingCols']); $i++) {
				if ($_GET['bSortable_' . intval($_GET['iSortCol_' . $i])] == "true") {
					$sortDir = (strcasecmp($_GET['sSortDir_' . $i], 'ASC') == 0) ? 'ASC' : 'DESC';
					$sOrder .= "CASE WHEN codigo LIKE 'JVC%' THEN 0 ELSE 1 END, ";
					$sOrder .= "`" . $columns[intval($_GET['iSortCol_' . $i])] . "` " . $sortDir . ", ";
				}
			}

			$sOrder = substr_replace($sOrder, "", -2);
			if ($sOrder == "ORDER BY") {
				$sOrder = "";
			}
		}

		// Si no hay orden definido, establecer el orden por defecto
		if (empty($sOrder)) {
			$sOrder = "ORDER BY CASE WHEN codigo LIKE 'JVC%' THEN 0 ELSE 1 END, codigo ASC";
		}

		// Filtering
		$sWhere = "";
		if (isset($_GET['sSearch']) && $_GET['sSearch'] != "") {
			$sWhere = "WHERE (";
			for ($i = 0; $i < count($columns); $i++) {
				if (isset($_GET['bSearchable_' . $i]) && $_GET['bSearchable_' . $i] == "true") {
					$sWhere .= "`" . $columns[$i] . "` LIKE :search OR ";
				}
			}
			$sWhere = substr_replace($sWhere, "", -3);
			$sWhere .= ')';
		}

		// Custom where
		if ($where) {
			$sWhere = $sWhere ? $sWhere . " AND " . $where : "WHERE " . $where;
		}

		// Build query
		$sQuery = "SELECT SQL_CALC_FOUND_ROWS `" . str_replace(" , ", " ", implode("`, `", $columns)) . "` 
                  FROM `" . $table . "` " . $sWhere . " " . $sOrder . " " . $sLimit;

		$statement = $this->_db->prepare($sQuery);

		// Bind parameters
		if (isset($_GET['sSearch']) && $_GET['sSearch'] != "") {
			$statement->bindValue(':search', '%' . $_GET['sSearch'] . '%', PDO::PARAM_STR);
		}

		$statement->execute();
		$rResult = $statement->fetchAll();

		// Data set length after filtering
		$sQuery = "SELECT FOUND_ROWS()";
		$statement = $this->_db->query($sQuery);
		$iFilteredTotal = $statement->fetchColumn();

		// Total data set length
		$sQuery = "SELECT COUNT(`" . $index_column . "`) FROM `" . $table . "`";
		$statement = $this->_db->query($sQuery);
		$iTotal = $statement->fetchColumn();

		// Output
		$output = array(
			"sEcho" => intval($_GET['sEcho']),
			"iTotalRecords" => $iTotal,
			"iTotalDisplayRecords" => $iFilteredTotal,
			"aaData" => array()
		);

		// Ordenar los resultados para asegurar que JVC aparezca primero
		usort($rResult, function ($a, $b) {
			$aIsJVC = strpos($a['codigo'], 'JVC') === 0;
			$bIsJVC = strpos($b['codigo'], 'JVC') === 0;

			if ($aIsJVC && !$bIsJVC)
				return -1;
			if (!$aIsJVC && $bIsJVC)
				return 1;
			return strcmp($a['codigo'], $b['codigo']);
		});

		foreach ($rResult as $aRow) {
			$row = array();
			for ($i = 0; $i < count($columns); $i++) {
				if ($columns[$i] != ' ') {
					$row[] = $aRow[$columns[$i]];
				}
			}
			$output['aaData'][] = $row;
		}

		return $output;
	}
	public function get($table, $index_column, $columns, $isExtra = false, $condicional = "", $where = false)
	{


		// Paging
		$sLimit = "";
		if (isset($_GET['iDisplayStart']) && $_GET['iDisplayLength'] != '-1') {
			$sLimit = "LIMIT " . intval($_GET['iDisplayStart']) . ", " . intval($_GET['iDisplayLength']);
		}

		// Ordering
		$sOrder = "";
		if (isset($_GET['iSortCol_0'])) {
			$sOrder = "ORDER BY  ";
			for ($i = 0; $i < intval($_GET['iSortingCols']); $i++) {
				if ($_GET['bSortable_' . intval($_GET['iSortCol_' . $i])] == "true") {
					$sortDir = (strcasecmp($_GET['sSortDir_' . $i], 'ASC') == 0) ? 'ASC' : 'DESC';
					$sOrder .= "`" . $columns[intval($_GET['iSortCol_' . $i])] . "` " . $sortDir . ", ";
				}
			}

			$sOrder = substr_replace($sOrder, "", -2);
			if ($sOrder == "ORDER BY") {
				$sOrder = "";
			}
		}

		/* 
		 * Filtering
		 * NOTE this does not match the built-in DataTables filtering which does it
		 * word by word on any field. It's possible to do here, but concerned about efficiency
		 * on very large tables, and MySQL's regex functionality is very limited
		 */
		$sWhere = "";
		if (isset($_GET['sSearch']) && $_GET['sSearch'] != "") {
			$sWhere = "WHERE (";
			for ($i = 0; $i < count($columns); $i++) {
				if (isset($_GET['bSearchable_' . $i]) && $_GET['bSearchable_' . $i] == "true") {
					$sWhere .= "`" . $columns[$i] . "` LIKE :search OR ";
				}
			}
			$sWhere = substr_replace($sWhere, "", -3);
			$sWhere .= ')';
		}

		// Individual column filtering
		for ($i = 0; $i < count($columns); $i++) {
			if (isset($_GET['bSearchable_' . $i]) && $_GET['bSearchable_' . $i] == "true" && $_GET['sSearch_' . $i] != '') {
				if ($sWhere == "") {
					$sWhere = "WHERE ";
				} else {
					$sWhere .= " AND ";
				}
				$sWhere .= "`" . $columns[$i] . "` LIKE :search" . $i . " ";
			}
		}
		if ($isExtra) {
			if ($sWhere == "") {
				$sWhere = "WHERE ";
			} else {
				$sWhere .= " AND ";
			}
			$sWhere .= ' ' . $condicional . ' ';
		}

		if ($where !== false) {
			$sWhere = $where;
		}
		// SQL queries get data to display
		$sQuery = "SELECT SQL_CALC_FOUND_ROWS `" . str_replace(" , ", " ", implode("`, `", $columns)) . "` FROM `" . $table . "` " . $sWhere . " " . $sOrder . " " . $sLimit;

		$statement = $this->_db->prepare($sQuery);

		// Bind parameters
		if (isset($_GET['sSearch']) && $_GET['sSearch'] != "") {
			$statement->bindValue(':search', '%' . $_GET['sSearch'] . '%', PDO::PARAM_STR);
		}
		for ($i = 0; $i < count($columns); $i++) {
			if (isset($_GET['bSearchable_' . $i]) && $_GET['bSearchable_' . $i] == "true" && $_GET['sSearch_' . $i] != '') {
				$statement->bindValue(':search' . $i, '%' . $_GET['sSearch_' . $i] . '%', PDO::PARAM_STR);
			}
		}

		$statement->execute();
		$rResult = $statement->fetchAll();

		$iFilteredTotal = current($this->_db->query('SELECT FOUND_ROWS()')->fetch());

		// Get total number of rows in table
		$sQuery = "SELECT COUNT(`" . $index_column . "`) FROM `" . $table . "`";
		//echo $sQuery;
		$iTotal = current($this->_db->query($sQuery)->fetch());

		// Output
		$output = array(
			"sEcho" => intval($_GET['sEcho']),
			"iTotalRecords" => $iTotal,
			"iTotalDisplayRecords" => $iFilteredTotal,
			"aaData" => array()
		);

		// Return array of values
		foreach ($rResult as $aRow) {
			$row = array();
			for ($i = 0; $i < count($columns); $i++) {
				if ($columns[$i] == "version") {
					// Special output formatting for 'version' column
					$row[] = ($aRow[$columns[$i]] == "0") ? '-' : $aRow[$columns[$i]];
				} else if ($columns[$i] != ' ') {
					$row[] = $aRow[$columns[$i]];
				}
			}
			$output['aaData'][] = $row;
		}

		echo json_encode($output);
	}
}
header('Pragma: no-cache');
header('Cache-Control: no-store, no-cache, must-revalidate');
// Create instance of TableData class

?>