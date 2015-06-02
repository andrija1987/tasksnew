<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg11.php" ?>
<?php include_once "ewmysql11.php" ?>
<?php include_once "phpfn11.php" ?>
<?php include_once "milestonesinfo.php" ?>
<?php include_once "usersinfo.php" ?>
<?php include_once "userfn11.php" ?>
<?php

//
// Page class
//

$milestones_add = NULL; // Initialize page object first

class cmilestones_add extends cmilestones {

	// Page ID
	var $PageID = 'add';

	// Project ID
	var $ProjectID = "{BFE33622-DA67-42BA-970D-459478D9EA4D}";

	// Table name
	var $TableName = 'milestones';

	// Page object name
	var $PageObjName = 'milestones_add';

	// Page name
	function PageName() {
		return ew_CurrentPage();
	}

	// Page URL
	function PageUrl() {
		$PageUrl = ew_CurrentPage() . "?";
		if ($this->UseTokenInUrl) $PageUrl .= "t=" . $this->TableVar . "&"; // Add page token
		return $PageUrl;
	}

	// Message
	function getMessage() {
		return @$_SESSION[EW_SESSION_MESSAGE];
	}

	function setMessage($v) {
		ew_AddMessage($_SESSION[EW_SESSION_MESSAGE], $v);
	}

	function getFailureMessage() {
		return @$_SESSION[EW_SESSION_FAILURE_MESSAGE];
	}

	function setFailureMessage($v) {
		ew_AddMessage($_SESSION[EW_SESSION_FAILURE_MESSAGE], $v);
	}

	function getSuccessMessage() {
		return @$_SESSION[EW_SESSION_SUCCESS_MESSAGE];
	}

	function setSuccessMessage($v) {
		ew_AddMessage($_SESSION[EW_SESSION_SUCCESS_MESSAGE], $v);
	}

	function getWarningMessage() {
		return @$_SESSION[EW_SESSION_WARNING_MESSAGE];
	}

	function setWarningMessage($v) {
		ew_AddMessage($_SESSION[EW_SESSION_WARNING_MESSAGE], $v);
	}

	// Show message
	function ShowMessage() {
		$hidden = FALSE;
		$html = "";

		// Message
		$sMessage = $this->getMessage();
		$this->Message_Showing($sMessage, "");
		if ($sMessage <> "") { // Message in Session, display
			if (!$hidden)
				$sMessage = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $sMessage;
			$html .= "<div class=\"alert alert-info ewInfo\">" . $sMessage . "</div>";
			$_SESSION[EW_SESSION_MESSAGE] = ""; // Clear message in Session
		}

		// Warning message
		$sWarningMessage = $this->getWarningMessage();
		$this->Message_Showing($sWarningMessage, "warning");
		if ($sWarningMessage <> "") { // Message in Session, display
			if (!$hidden)
				$sWarningMessage = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $sWarningMessage;
			$html .= "<div class=\"alert alert-warning ewWarning\">" . $sWarningMessage . "</div>";
			$_SESSION[EW_SESSION_WARNING_MESSAGE] = ""; // Clear message in Session
		}

		// Success message
		$sSuccessMessage = $this->getSuccessMessage();
		$this->Message_Showing($sSuccessMessage, "success");
		if ($sSuccessMessage <> "") { // Message in Session, display
			if (!$hidden)
				$sSuccessMessage = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $sSuccessMessage;
			$html .= "<div class=\"alert alert-success ewSuccess\">" . $sSuccessMessage . "</div>";
			$_SESSION[EW_SESSION_SUCCESS_MESSAGE] = ""; // Clear message in Session
		}

		// Failure message
		$sErrorMessage = $this->getFailureMessage();
		$this->Message_Showing($sErrorMessage, "failure");
		if ($sErrorMessage <> "") { // Message in Session, display
			if (!$hidden)
				$sErrorMessage = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $sErrorMessage;
			$html .= "<div class=\"alert alert-danger ewError\">" . $sErrorMessage . "</div>";
			$_SESSION[EW_SESSION_FAILURE_MESSAGE] = ""; // Clear message in Session
		}
		echo "<div class=\"ewMessageDialog\"" . (($hidden) ? " style=\"display: none;\"" : "") . ">" . $html . "</div>";
	}
	var $PageHeader;
	var $PageFooter;

	// Show Page Header
	function ShowPageHeader() {
		$sHeader = $this->PageHeader;
		$this->Page_DataRendering($sHeader);
		if ($sHeader <> "") { // Header exists, display
			echo "<p>" . $sHeader . "</p>";
		}
	}

	// Show Page Footer
	function ShowPageFooter() {
		$sFooter = $this->PageFooter;
		$this->Page_DataRendered($sFooter);
		if ($sFooter <> "") { // Footer exists, display
			echo "<p>" . $sFooter . "</p>";
		}
	}

	// Validate page request
	function IsPageRequest() {
		global $objForm;
		if ($this->UseTokenInUrl) {
			if ($objForm)
				return ($this->TableVar == $objForm->GetValue("t"));
			if (@$_GET["t"] <> "")
				return ($this->TableVar == $_GET["t"]);
		} else {
			return TRUE;
		}
	}
	var $Token = "";
	var $CheckToken = EW_CHECK_TOKEN;
	var $CheckTokenFn = "ew_CheckToken";
	var $CreateTokenFn = "ew_CreateToken";

	// Valid Post
	function ValidPost() {
		if (!$this->CheckToken || !ew_IsHttpPost())
			return TRUE;
		if (!isset($_POST[EW_TOKEN_NAME]))
			return FALSE;
		$fn = $this->CheckTokenFn;
		if (is_callable($fn))
			return $fn($_POST[EW_TOKEN_NAME]);
		return FALSE;
	}

	// Create Token
	function CreateToken() {
		global $gsToken;
		if ($this->CheckToken) {
			$fn = $this->CreateTokenFn;
			if ($this->Token == "" && is_callable($fn)) // Create token
				$this->Token = $fn();
			$gsToken = $this->Token; // Save to global variable
		}
	}

	//
	// Page class constructor
	//
	function __construct() {
		global $conn, $Language;
		$GLOBALS["Page"] = &$this;

		// Language object
		if (!isset($Language)) $Language = new cLanguage();

		// Parent constuctor
		parent::__construct();

		// Table object (milestones)
		if (!isset($GLOBALS["milestones"]) || get_class($GLOBALS["milestones"]) == "cmilestones") {
			$GLOBALS["milestones"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["milestones"];
		}

		// Table object (users)
		if (!isset($GLOBALS['users'])) $GLOBALS['users'] = new cusers();

		// User table object (users)
		if (!isset($GLOBALS["UserTable"])) $GLOBALS["UserTable"] = new cusers();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'add', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'milestones', TRUE);

		// Start timer
		if (!isset($GLOBALS["gTimer"])) $GLOBALS["gTimer"] = new cTimer();

		// Open connection
		if (!isset($conn)) $conn = ew_Connect();
	}

	// 
	//  Page_Init
	//
	function Page_Init() {
		global $gsExport, $gsCustomExport, $gsExportFile, $UserProfile, $Language, $Security, $objForm;

		// Security
		$Security = new cAdvancedSecurity();
		if (!$Security->IsLoggedIn()) $Security->AutoLogin();
		if (!$Security->IsLoggedIn()) {
			$Security->SaveLastUrl();
			$this->Page_Terminate(ew_GetUrl("login.php"));
		}
		$Security->TablePermission_Loading();
		$Security->LoadCurrentUserLevel($this->ProjectID . $this->TableName);
		$Security->TablePermission_Loaded();

		// Create form object
		$objForm = new cFormObj();
		$this->CurrentAction = (@$_GET["a"] <> "") ? $_GET["a"] : @$_POST["a_list"]; // Set up current action

		// Global Page Loading event (in userfn*.php)
		Page_Loading();

		// Page Load event
		$this->Page_Load();

		// Check token
		if (!$this->ValidPost()) {
			echo $Language->Phrase("InvalidPostRequest");
			$this->Page_Terminate();
			exit();
		}

		// Process auto fill
		if (@$_POST["ajax"] == "autofill") {
			$results = $this->GetAutoFill(@$_POST["name"], @$_POST["q"]);
			if ($results) {

				// Clean output buffer
				if (!EW_DEBUG_ENABLED && ob_get_length())
					ob_end_clean();
				echo $results;
				$this->Page_Terminate();
				exit();
			}
		}

		// Create Token
		$this->CreateToken();
	}

	//
	// Page_Terminate
	//
	function Page_Terminate($url = "") {
		global $conn, $gsExportFile, $gTmpImages;

		// Page Unload event
		$this->Page_Unload();

		// Global Page Unloaded event (in userfn*.php)
		Page_Unloaded();

		// Export
		global $EW_EXPORT, $milestones;
		if ($this->CustomExport <> "" && $this->CustomExport == $this->Export && array_key_exists($this->CustomExport, $EW_EXPORT)) {
				$sContent = ob_get_contents();
			if ($gsExportFile == "") $gsExportFile = $this->TableVar;
			$class = $EW_EXPORT[$this->CustomExport];
			if (class_exists($class)) {
				$doc = new $class($milestones);
				$doc->Text = $sContent;
				if ($this->Export == "email")
					echo $this->ExportEmail($doc->Text);
				else
					$doc->Export();
				ew_DeleteTmpImages(); // Delete temp images
				exit();
			}
		}
		$this->Page_Redirecting($url);

		 // Close connection
		$conn->Close();

		// Go to URL if specified
		if ($url <> "") {
			if (!EW_DEBUG_ENABLED && ob_get_length())
				ob_end_clean();
			header("Location: " . $url);
		}
		exit();
	}
	var $DbMasterFilter = "";
	var $DbDetailFilter = "";
	var $StartRec;
	var $Priv = 0;
	var $OldRecordset;
	var $CopyRecord;

	// 
	// Page main
	//
	function Page_Main() {
		global $objForm, $Language, $gsFormError;

		// Process form if post back
		if (@$_POST["a_add"] <> "") {
			$this->CurrentAction = $_POST["a_add"]; // Get form action
			$this->CopyRecord = $this->LoadOldRecord(); // Load old recordset
			$this->LoadFormValues(); // Load form values
		} else { // Not post back

			// Load key values from QueryString
			$this->CopyRecord = TRUE;
			if (@$_GET["milestoneid"] != "") {
				$this->milestoneid->setQueryStringValue($_GET["milestoneid"]);
				$this->setKey("milestoneid", $this->milestoneid->CurrentValue); // Set up key
			} else {
				$this->setKey("milestoneid", ""); // Clear key
				$this->CopyRecord = FALSE;
			}
			if ($this->CopyRecord) {
				$this->CurrentAction = "C"; // Copy record
			} else {
				$this->CurrentAction = "I"; // Display blank record
				$this->LoadDefaultValues(); // Load default values
			}
		}

		// Set up Breadcrumb
		$this->SetupBreadcrumb();

		// Validate form if post back
		if (@$_POST["a_add"] <> "") {
			if (!$this->ValidateForm()) {
				$this->CurrentAction = "I"; // Form error, reset action
				$this->EventCancelled = TRUE; // Event cancelled
				$this->RestoreFormValues(); // Restore form values
				$this->setFailureMessage($gsFormError);
			}
		}

		// Perform action based on action code
		switch ($this->CurrentAction) {
			case "I": // Blank record, no action required
				break;
			case "C": // Copy an existing record
				if (!$this->LoadRow()) { // Load record based on key
					if ($this->getFailureMessage() == "") $this->setFailureMessage($Language->Phrase("NoRecord")); // No record found
					$this->Page_Terminate("milestoneslist.php"); // No matching record, return to list
				}
				break;
			case "A": // Add new record
				$this->SendEmail = TRUE; // Send email on add success
				if ($this->AddRow($this->OldRecordset)) { // Add successful
					if ($this->getSuccessMessage() == "")
						$this->setSuccessMessage($Language->Phrase("AddSuccess")); // Set up success message
					$sReturnUrl = $this->getReturnUrl();
					if (ew_GetPageName($sReturnUrl) == "milestonesview.php")
						$sReturnUrl = $this->GetViewUrl(); // View paging, return to view page with keyurl directly
					$this->Page_Terminate($sReturnUrl); // Clean up and return
				} else {
					$this->EventCancelled = TRUE; // Event cancelled
					$this->RestoreFormValues(); // Add failed, restore form values
				}
		}

		// Render row based on row type
		$this->RowType = EW_ROWTYPE_ADD;  // Render add type

		// Render row
		$this->ResetAttrs();
		$this->RenderRow();
	}

	// Get upload files
	function GetUploadFiles() {
		global $objForm, $Language;

		// Get upload data
	}

	// Load default values
	function LoadDefaultValues() {
		$this->milestonename->CurrentValue = NULL;
		$this->milestonename->OldValue = $this->milestonename->CurrentValue;
		$this->startdate->CurrentValue = "0000-00-00";
		$this->enddate->CurrentValue = "0000-00-00";
	}

	// Load form values
	function LoadFormValues() {

		// Load from form
		global $objForm;
		if (!$this->milestonename->FldIsDetailKey) {
			$this->milestonename->setFormValue($objForm->GetValue("x_milestonename"));
		}
		if (!$this->startdate->FldIsDetailKey) {
			$this->startdate->setFormValue($objForm->GetValue("x_startdate"));
			$this->startdate->CurrentValue = ew_UnFormatDateTime($this->startdate->CurrentValue, 5);
		}
		if (!$this->enddate->FldIsDetailKey) {
			$this->enddate->setFormValue($objForm->GetValue("x_enddate"));
			$this->enddate->CurrentValue = ew_UnFormatDateTime($this->enddate->CurrentValue, 5);
		}
	}

	// Restore form values
	function RestoreFormValues() {
		global $objForm;
		$this->LoadOldRecord();
		$this->milestonename->CurrentValue = $this->milestonename->FormValue;
		$this->startdate->CurrentValue = $this->startdate->FormValue;
		$this->startdate->CurrentValue = ew_UnFormatDateTime($this->startdate->CurrentValue, 5);
		$this->enddate->CurrentValue = $this->enddate->FormValue;
		$this->enddate->CurrentValue = ew_UnFormatDateTime($this->enddate->CurrentValue, 5);
	}

	// Load row based on key values
	function LoadRow() {
		global $conn, $Security, $Language;
		$sFilter = $this->KeyFilter();

		// Call Row Selecting event
		$this->Row_Selecting($sFilter);

		// Load SQL based on filter
		$this->CurrentFilter = $sFilter;
		$sSql = $this->SQL();
		$res = FALSE;
		$rs = ew_LoadRecordset($sSql);
		if ($rs && !$rs->EOF) {
			$res = TRUE;
			$this->LoadRowValues($rs); // Load row values
			$rs->Close();
		}
		return $res;
	}

	// Load row values from recordset
	function LoadRowValues(&$rs) {
		global $conn;
		if (!$rs || $rs->EOF) return;

		// Call Row Selected event
		$row = &$rs->fields;
		$this->Row_Selected($row);
		$this->milestoneid->setDbValue($rs->fields('milestoneid'));
		$this->milestonename->setDbValue($rs->fields('milestonename'));
		$this->startdate->setDbValue($rs->fields('startdate'));
		$this->enddate->setDbValue($rs->fields('enddate'));
	}

	// Load DbValue from recordset
	function LoadDbValues(&$rs) {
		if (!$rs || !is_array($rs) && $rs->EOF) return;
		$row = is_array($rs) ? $rs : $rs->fields;
		$this->milestoneid->DbValue = $row['milestoneid'];
		$this->milestonename->DbValue = $row['milestonename'];
		$this->startdate->DbValue = $row['startdate'];
		$this->enddate->DbValue = $row['enddate'];
	}

	// Load old record
	function LoadOldRecord() {

		// Load key values from Session
		$bValidKey = TRUE;
		if (strval($this->getKey("milestoneid")) <> "")
			$this->milestoneid->CurrentValue = $this->getKey("milestoneid"); // milestoneid
		else
			$bValidKey = FALSE;

		// Load old recordset
		if ($bValidKey) {
			$this->CurrentFilter = $this->KeyFilter();
			$sSql = $this->SQL();
			$this->OldRecordset = ew_LoadRecordset($sSql);
			$this->LoadRowValues($this->OldRecordset); // Load row values
		} else {
			$this->OldRecordset = NULL;
		}
		return $bValidKey;
	}

	// Render row values based on field settings
	function RenderRow() {
		global $conn, $Security, $Language;
		global $gsLanguage;

		// Initialize URLs
		// Call Row_Rendering event

		$this->Row_Rendering();

		// Common render codes for all row types
		// milestoneid
		// milestonename
		// startdate
		// enddate

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

			// milestoneid
			$this->milestoneid->ViewValue = $this->milestoneid->CurrentValue;
			$this->milestoneid->ViewCustomAttributes = "";

			// milestonename
			$this->milestonename->ViewValue = $this->milestonename->CurrentValue;
			$this->milestonename->ViewCustomAttributes = "";

			// startdate
			$this->startdate->ViewValue = $this->startdate->CurrentValue;
			$this->startdate->ViewValue = ew_FormatDateTime($this->startdate->ViewValue, 5);
			$this->startdate->ViewCustomAttributes = "";

			// enddate
			$this->enddate->ViewValue = $this->enddate->CurrentValue;
			$this->enddate->ViewValue = ew_FormatDateTime($this->enddate->ViewValue, 5);
			$this->enddate->ViewCustomAttributes = "";

			// milestonename
			$this->milestonename->LinkCustomAttributes = "";
			$this->milestonename->HrefValue = "";
			$this->milestonename->TooltipValue = "";

			// startdate
			$this->startdate->LinkCustomAttributes = "";
			$this->startdate->HrefValue = "";
			$this->startdate->TooltipValue = "";

			// enddate
			$this->enddate->LinkCustomAttributes = "";
			$this->enddate->HrefValue = "";
			$this->enddate->TooltipValue = "";
		} elseif ($this->RowType == EW_ROWTYPE_ADD) { // Add row

			// milestonename
			$this->milestonename->EditAttrs["class"] = "form-control";
			$this->milestonename->EditCustomAttributes = "";
			$this->milestonename->EditValue = ew_HtmlEncode($this->milestonename->CurrentValue);
			$this->milestonename->PlaceHolder = ew_RemoveHtml($this->milestonename->FldCaption());

			// startdate
			$this->startdate->EditAttrs["class"] = "form-control";
			$this->startdate->EditCustomAttributes = "";
			$this->startdate->EditValue = ew_HtmlEncode(ew_FormatDateTime($this->startdate->CurrentValue, 5));
			$this->startdate->PlaceHolder = ew_RemoveHtml($this->startdate->FldCaption());

			// enddate
			$this->enddate->EditAttrs["class"] = "form-control";
			$this->enddate->EditCustomAttributes = "";
			$this->enddate->EditValue = ew_HtmlEncode(ew_FormatDateTime($this->enddate->CurrentValue, 5));
			$this->enddate->PlaceHolder = ew_RemoveHtml($this->enddate->FldCaption());

			// Edit refer script
			// milestonename

			$this->milestonename->HrefValue = "";

			// startdate
			$this->startdate->HrefValue = "";

			// enddate
			$this->enddate->HrefValue = "";
		}
		if ($this->RowType == EW_ROWTYPE_ADD ||
			$this->RowType == EW_ROWTYPE_EDIT ||
			$this->RowType == EW_ROWTYPE_SEARCH) { // Add / Edit / Search row
			$this->SetupFieldTitles();
		}

		// Call Row Rendered event
		if ($this->RowType <> EW_ROWTYPE_AGGREGATEINIT)
			$this->Row_Rendered();
	}

	// Validate form
	function ValidateForm() {
		global $Language, $gsFormError;

		// Initialize form error message
		$gsFormError = "";

		// Check if validation required
		if (!EW_SERVER_VALIDATE)
			return ($gsFormError == "");
		if (!$this->milestonename->FldIsDetailKey && !is_null($this->milestonename->FormValue) && $this->milestonename->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->milestonename->FldCaption(), $this->milestonename->ReqErrMsg));
		}
		if (!$this->startdate->FldIsDetailKey && !is_null($this->startdate->FormValue) && $this->startdate->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->startdate->FldCaption(), $this->startdate->ReqErrMsg));
		}
		if (!ew_CheckDate($this->startdate->FormValue)) {
			ew_AddMessage($gsFormError, $this->startdate->FldErrMsg());
		}
		if (!$this->enddate->FldIsDetailKey && !is_null($this->enddate->FormValue) && $this->enddate->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->enddate->FldCaption(), $this->enddate->ReqErrMsg));
		}
		if (!ew_CheckDate($this->enddate->FormValue)) {
			ew_AddMessage($gsFormError, $this->enddate->FldErrMsg());
		}

		// Return validate result
		$ValidateForm = ($gsFormError == "");

		// Call Form_CustomValidate event
		$sFormCustomError = "";
		$ValidateForm = $ValidateForm && $this->Form_CustomValidate($sFormCustomError);
		if ($sFormCustomError <> "") {
			ew_AddMessage($gsFormError, $sFormCustomError);
		}
		return $ValidateForm;
	}

	// Add record
	function AddRow($rsold = NULL) {
		global $conn, $Language, $Security;

		// Load db values from rsold
		if ($rsold) {
			$this->LoadDbValues($rsold);
		}
		$rsnew = array();

		// milestonename
		$this->milestonename->SetDbValueDef($rsnew, $this->milestonename->CurrentValue, "", FALSE);

		// startdate
		$this->startdate->SetDbValueDef($rsnew, ew_UnFormatDateTime($this->startdate->CurrentValue, 5), ew_CurrentDate(), strval($this->startdate->CurrentValue) == "");

		// enddate
		$this->enddate->SetDbValueDef($rsnew, ew_UnFormatDateTime($this->enddate->CurrentValue, 5), ew_CurrentDate(), strval($this->enddate->CurrentValue) == "");

		// Call Row Inserting event
		$rs = ($rsold == NULL) ? NULL : $rsold->fields;
		$bInsertRow = $this->Row_Inserting($rs, $rsnew);
		if ($bInsertRow) {
			$conn->raiseErrorFn = $GLOBALS["EW_ERROR_FN"];
			$AddRow = $this->Insert($rsnew);
			$conn->raiseErrorFn = '';
			if ($AddRow) {
			}
		} else {
			if ($this->getSuccessMessage() <> "" || $this->getFailureMessage() <> "") {

				// Use the message, do nothing
			} elseif ($this->CancelMessage <> "") {
				$this->setFailureMessage($this->CancelMessage);
				$this->CancelMessage = "";
			} else {
				$this->setFailureMessage($Language->Phrase("InsertCancelled"));
			}
			$AddRow = FALSE;
		}

		// Get insert id if necessary
		if ($AddRow) {
			$this->milestoneid->setDbValue($conn->Insert_ID());
			$rsnew['milestoneid'] = $this->milestoneid->DbValue;
		}
		if ($AddRow) {

			// Call Row Inserted event
			$rs = ($rsold == NULL) ? NULL : $rsold->fields;
			$this->Row_Inserted($rs, $rsnew);
		}
		return $AddRow;
	}

	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $Breadcrumb, $Language;
		$Breadcrumb = new cBreadcrumb();
		$url = substr(ew_CurrentUrl(), strrpos(ew_CurrentUrl(), "/")+1);
		$Breadcrumb->Add("list", $this->TableVar, "milestoneslist.php", "", $this->TableVar, TRUE);
		$PageId = ($this->CurrentAction == "C") ? "Copy" : "Add";
		$Breadcrumb->Add("add", $PageId, $url);
	}

	// Page Load event
	function Page_Load() {

		//echo "Page Load";
	}

	// Page Unload event
	function Page_Unload() {

		//echo "Page Unload";
	}

	// Page Redirecting event
	function Page_Redirecting(&$url) {

		// Example:
		//$url = "your URL";

	}

	// Message Showing event
	// $type = ''|'success'|'failure'|'warning'
	function Message_Showing(&$msg, $type) {
		if ($type == 'success') {

			//$msg = "your success message";
		} elseif ($type == 'failure') {

			//$msg = "your failure message";
		} elseif ($type == 'warning') {

			//$msg = "your warning message";
		} else {

			//$msg = "your message";
		}
	}

	// Page Render event
	function Page_Render() {

		//echo "Page Render";
	}

	// Page Data Rendering event
	function Page_DataRendering(&$header) {

		// Example:
		//$header = "your header";

	}

	// Page Data Rendered event
	function Page_DataRendered(&$footer) {

		// Example:
		//$footer = "your footer";

	}

	// Form Custom Validate event
	function Form_CustomValidate(&$CustomError) {

		// Return error message in CustomError
		return TRUE;
	}
}
?>
<?php ew_Header(FALSE) ?>
<?php

// Create page object
if (!isset($milestones_add)) $milestones_add = new cmilestones_add();

// Page init
$milestones_add->Page_Init();

// Page main
$milestones_add->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$milestones_add->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var milestones_add = new ew_Page("milestones_add");
milestones_add.PageID = "add"; // Page ID
var EW_PAGE_ID = milestones_add.PageID; // For backward compatibility

// Form object
var fmilestonesadd = new ew_Form("fmilestonesadd");

// Validate form
fmilestonesadd.Validate = function() {
	if (!this.ValidateRequired)
		return true; // Ignore validation
	var $ = jQuery, fobj = this.GetForm(), $fobj = $(fobj);
	this.PostAutoSuggest();
	if ($fobj.find("#a_confirm").val() == "F")
		return true;
	var elm, felm, uelm, addcnt = 0;
	var $k = $fobj.find("#" + this.FormKeyCountName); // Get key_count
	var rowcnt = ($k[0]) ? parseInt($k.val(), 10) : 1;
	var startcnt = (rowcnt == 0) ? 0 : 1; // Check rowcnt == 0 => Inline-Add
	var gridinsert = $fobj.find("#a_list").val() == "gridinsert";
	for (var i = startcnt; i <= rowcnt; i++) {
		var infix = ($k[0]) ? String(i) : "";
		$fobj.data("rowindex", infix);
			elm = this.GetElements("x" + infix + "_milestonename");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $milestones->milestonename->FldCaption(), $milestones->milestonename->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_startdate");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $milestones->startdate->FldCaption(), $milestones->startdate->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_startdate");
			if (elm && !ew_CheckDate(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($milestones->startdate->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_enddate");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $milestones->enddate->FldCaption(), $milestones->enddate->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_enddate");
			if (elm && !ew_CheckDate(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($milestones->enddate->FldErrMsg()) ?>");

			// Set up row object
			ew_ElementsToRow(fobj);

			// Fire Form_CustomValidate event
			if (!this.Form_CustomValidate(fobj))
				return false;
	}

	// Process detail forms
	var dfs = $fobj.find("input[name='detailpage']").get();
	for (var i = 0; i < dfs.length; i++) {
		var df = dfs[i], val = df.value;
		if (val && ewForms[val])
			if (!ewForms[val].Validate())
				return false;
	}
	return true;
}

// Form_CustomValidate event
fmilestonesadd.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fmilestonesadd.ValidateRequired = true;
<?php } else { ?>
fmilestonesadd.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
// Form object for search

</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<div class="ewToolbar">
<?php $Breadcrumb->Render(); ?>
<?php echo $Language->SelectionForm(); ?>
<div class="clearfix"></div>
</div>
<?php $milestones_add->ShowPageHeader(); ?>
<?php
$milestones_add->ShowMessage();
?>
<form name="fmilestonesadd" id="fmilestonesadd" class="form-horizontal ewForm ewAddForm" action="<?php echo ew_CurrentPage() ?>" method="post">
<?php if ($milestones_add->CheckToken) { ?>
<input type="hidden" name="<?php echo EW_TOKEN_NAME ?>" value="<?php echo $milestones_add->Token ?>">
<?php } ?>
<input type="hidden" name="t" value="milestones">
<input type="hidden" name="a_add" id="a_add" value="A">
<div>
<?php if ($milestones->milestonename->Visible) { // milestonename ?>
	<div id="r_milestonename" class="form-group">
		<label id="elh_milestones_milestonename" for="x_milestonename" class="col-sm-2 control-label ewLabel"><?php echo $milestones->milestonename->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $milestones->milestonename->CellAttributes() ?>>
<span id="el_milestones_milestonename">
<input type="text" data-field="x_milestonename" name="x_milestonename" id="x_milestonename" size="30" maxlength="10" placeholder="<?php echo ew_HtmlEncode($milestones->milestonename->PlaceHolder) ?>" value="<?php echo $milestones->milestonename->EditValue ?>"<?php echo $milestones->milestonename->EditAttributes() ?>>
</span>
<?php echo $milestones->milestonename->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($milestones->startdate->Visible) { // startdate ?>
	<div id="r_startdate" class="form-group">
		<label id="elh_milestones_startdate" for="x_startdate" class="col-sm-2 control-label ewLabel"><?php echo $milestones->startdate->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $milestones->startdate->CellAttributes() ?>>
<span id="el_milestones_startdate">
<input type="text" data-field="x_startdate" name="x_startdate" id="x_startdate" placeholder="<?php echo ew_HtmlEncode($milestones->startdate->PlaceHolder) ?>" value="<?php echo $milestones->startdate->EditValue ?>"<?php echo $milestones->startdate->EditAttributes() ?>>
<?php if (!$milestones->startdate->ReadOnly && !$milestones->startdate->Disabled && !isset($milestones->startdate->EditAttrs["readonly"]) && !isset($milestones->startdate->EditAttrs["disabled"])) { ?>
<script type="text/javascript">
ew_CreateCalendar("fmilestonesadd", "x_startdate", "%Y/%m/%d");
</script>
<?php } ?>
</span>
<?php echo $milestones->startdate->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($milestones->enddate->Visible) { // enddate ?>
	<div id="r_enddate" class="form-group">
		<label id="elh_milestones_enddate" for="x_enddate" class="col-sm-2 control-label ewLabel"><?php echo $milestones->enddate->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $milestones->enddate->CellAttributes() ?>>
<span id="el_milestones_enddate">
<input type="text" data-field="x_enddate" name="x_enddate" id="x_enddate" placeholder="<?php echo ew_HtmlEncode($milestones->enddate->PlaceHolder) ?>" value="<?php echo $milestones->enddate->EditValue ?>"<?php echo $milestones->enddate->EditAttributes() ?>>
<?php if (!$milestones->enddate->ReadOnly && !$milestones->enddate->Disabled && !isset($milestones->enddate->EditAttrs["readonly"]) && !isset($milestones->enddate->EditAttrs["disabled"])) { ?>
<script type="text/javascript">
ew_CreateCalendar("fmilestonesadd", "x_enddate", "%Y/%m/%d");
</script>
<?php } ?>
</span>
<?php echo $milestones->enddate->CustomMsg ?></div></div>
	</div>
<?php } ?>
</div>
<div class="form-group">
	<div class="col-sm-offset-2 col-sm-10">
<button class="btn btn-primary ewButton" name="btnAction" id="btnAction" type="submit"><?php echo $Language->Phrase("AddBtn") ?></button>
	</div>
</div>
</form>
<script type="text/javascript">
fmilestonesadd.Init();
</script>
<?php
$milestones_add->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$milestones_add->Page_Terminate();
?>
