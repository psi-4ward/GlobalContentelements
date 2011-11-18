<?php if (!defined('TL_ROOT')) die('You cannot access this file directly!');

/**
 * GlobalContentelements
 * Use contentelements in every extension
 *
 * @author Christoph Wiechert <wio@psitrax.de>
 * @copyright 4ward.media 2011
 * @licence LGPL
 */


/**
 * Helperclass to handle for the GlobalContentelements
 * @author Christoph Wiechert <wio@psitrax.de>
 */
class GlobalContentElements extends System
{

	/**
	 * Callback to save the modules name in the tl_content.do row
	 * @param DataContainer $dc
	 * @return void
	 */
	public function setDo($dc)
	{
		// Return if there is no active record (override all)
		if (!$dc->activeRecord)
		{
			return;
		}
		$this->import('Database');
		$this->Database->prepare("UPDATE tl_content SET do=? WHERE id=?")->execute($this->Input->get('do'),$dc->id);
	}


	/**
	 * Callback to preserve the tl_content entities which belongs
	 * to other modules
	 *
	 * @param string $strTable
	 * @param array $new_records
	 * @param string $ptable
	 * @param array $ctable
	 * @return bool
	 */
	public function reviseTable($strTable, $new_records,&$ptable,&$ctable)
	{
		$reload = false;

		// only for tl_content table or child-table
		if($strTable != 'tl_content' && !in_array('tl_content',$ctable)) return $reload;

		$this->import('Database');

		// Delete all records of the current table that are not related to the parent table
		if (strlen($ptable))
		{
			$objStmt = $this->Database->execute
			("
				DELETE FROM " . $strTable . "
				WHERE
					NOT EXISTS (SELECT * FROM " . $ptable . " WHERE " . $strTable . ".pid = " . $ptable . ".id)"
					.(($strTable == 'tl_content') ? " AND do='".$this->Input->get('do')."'" : '' )
			);

			if ($objStmt->affectedRows > 0)
			{
				$reload = true;
			}
		}

		// Delete all records of the child table that are not related to the current table
		if (is_array($ctable) && count($ctable))
		{
			foreach ($ctable as $v)
			{
				if (strlen($v))
				{
					$objStmt = $this->Database->execute
					("
						DELETE FROM " . $v . "
						WHERE
							NOT EXISTS (SELECT * FROM " . $strTable . " WHERE " . $v . ".pid = " . $strTable . ".id)"
							.(($v == 'tl_content') ? " AND do='".$this->Input->get('do')."'" : '' )
					);
					if ($objStmt->affectedRows > 0)
					{
						$reload = true;
					}
				}
			}
		}

		// Set $ptable and $ctable to an empty string to prevent DC_Table::reviseTable() doing its deletion
		$ptable = $ctable = '';
		return $reload;
	}
}

?>