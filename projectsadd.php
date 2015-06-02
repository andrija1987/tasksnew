<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg11.php" ?>
<?php include_once "ewmysql11.php" ?>
<?php include_once "phpfn11.php" ?>
<?php include_once "projectsinfo.php" ?>
<?php include_once "usersinfo.php" ?>
<?php include_once "userfn11.php" ?>
<?php

//
// Page class
//

$projects_add = NULL; // Initialize page object first

class cprojects_add extends cprojects {

	// Page ID
	var $PageID = 'add';

	// Project ID
	var $ProjectID = "{BFE33622-DA67-42BA-970D-459478D9EA4D}";

	// Table name
	var $TableName = 'projects';

	// Page object name
	var $PageObjName = 'projects_add';

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

		// Table object (projects)
		if (!isset($GLOBALS["projects"]) || get_class($GLOBALS["projects"]) == "cprojects") {
			$GLOBALS["projects"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["projects"];
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
			define("EW_TABLE_NAME", 'projects', TRUE);

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
		global $EW_EXPORT, $projects;
		if ($this->CustomExport <> "" && $this->CustomExport == $this->Export && array_key_exists($this->CustomExport, $EW_EXPORT)) {
				$sContent = ob_get_contents();
			if ($gsExportFile == "") $gsExportFile = $this->TableVar;
			$class = $EW_EXPORT[$this->CustomExport];
			if (class_exists($class)) {
				$doc = new $class($projects);
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
			if (@$_GET["projectid"] != "") {
				$this->projectid->setQueryStringValue($_GET["projectid"]);
				$this->setKey("projectid", $this->projectid->CurrentValue); // Set up key
			} else {
				$this->setKey("projectid", ""); // Clear key
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
					$this->Page_Terminate("projectslist.php"); // No matching record, return to list
				}
				break;
			case "A": // Add new record
				$this->SendEmail = TRUE; // Send email on add success
				if ($this->AddRow($this->OldRecordset)) { // Add successful
					if ($this->getSuccessMessage() == "")
						$this->setSuccessMessage($Language->Phrase("AddSuccess")); // Set up success message
					$sReturnUrl = $this->getReturnUrl();
					if (ew_GetPageName($sReturnUrl) == "projectsview.php")
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
		$this->projectname->CurrentValue = NULL;
		$this->projectname->OldValue = $this->projectname->CurrentValue;
		$this->projectstartdate->CurrentValue = NULL;
		$this->projectstartdate->OldValue = $this->projectstartdate->CurrentValue;
		$this->projectenddate->CurrentValue = NULL;
		$this->projectenddate->OldValue = $this->projectenddate->CurrentValue;
		$this->budget->CurrentValue = NULL;
		$this->budget->OldValue = $this->budget->CurrentValue;
	}

	// Load form values
	function LoadFormValues() {

		// Load from form
		global $objForm;
		if (!$this->projectname->FldIsDetailKey) {
			$this->projectname->setFormValue($objForm->GetValue("x_projectname"));
		}
		if (!$this->projectstartdate->FldIsDetailKey) {
			$this->projectstartdate->setFormValue($objForm->GetValue("x_projectstartdate"));
			$this->projectstartdate->CurrentValue = ew_UnFormatDateTime($this->projectstartdate->CurrentValue, 5);
		}
		if (!$this->projectenddate->FldIsDetailKey) {
			$this->projectenddate->setFormValue($objForm->GetValue("x_projectenddate"));
			$this->projectenddate->CurrentValue = ew_UnFormatDateTime($this->projectenddate->CurrentValue, 5);
		}
		if (!$this->budget->FldIsDetailKey) {
			$this->budget->setFormValue($objForm->GetValue("x_budget"));
		}
	}

	// Restore form values
	function RestoreFormValues() {
		global $objForm;
		$this->LoadOldRecord();
		$this->projectname->CurrentValue = $this->projectname->FormValue;
		$this->projectstartdate->CurrentValue = $this->projectstartdate->FormValue;
		$this->projectstartdate->CurrentValue = ew_UnFormatDateTime($this->projectstartdate->CurrentValue, 5);
		$this->projectenddate->CurrentValue = $this->projectenddate->FormValue;
		$this->projectenddate->CurrentValue = ew_UnFormatDateTime($this->projectenddate->CurrentValue, 5);
		$this->budget->CurrentValue = $this->budget->FormValue;
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
		$this->projectid->setDbValue($rs->fields('projectid'));
		$this->projectname->setDbValue($rs->fields('projectname'));
		$this->projectstartdate->setDbValue($rs->fields('projectstartdate'));
		$this->projectenddate->setDbValue($rs->fields('projectenddate'));
		$this->budget->setDbValue($rs->fields('budget'));
	}

	// Load DbValue from recordset
	function LoadDbValues(&$rs) {
		if (!$rs || !is_array($rs) && $rs->EOF) return;
		$row = is_array($rs) ? $rs : $rs->fields;
		$this->projectid->DbValue = $row['projectid'];
		$this->projectname->DbValue = $row['projectname'];
		$this->projectstartdate->DbValue = $row['projectstartdate'];
		$this->projectenddate->DbValue = $row['projectenddate'];
		$this->budget->DbValue = $row['budget'];
	}

	// Load old record
	function LoadOldRecord() {

		// Load key values from Session
		$bValidKey = TRUE;
		if (strval($this->getKey("projectid")) <> "")
			$this->projectid->CurrentValue = $this->getKey("projectid"); // projectid
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
		// projectid
		// projectname
		// projectstartdate
		// projectenddate
		// budget

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

			// projectid
			$this->projectid->ViewValue = $this->projectid->CurrentValue;
			$this->projectid->ViewCustomAttributes = "";

			// projectname
			$this->projectname->ViewValue = $this->projectname->CurrentValue;
			$this->projectname->ViewCustomAttributes = "";

			// projectstartdate
			$this->projectstartdate->ViewValue = $this->projectstartdate->CurrentValue;
			$this->projectstartdate->ViewValue = ew_FormatDateTime($this->projectstartdate->ViewValue, 5);
			$this->projectstartdate->ViewCustomAttributes = "";

			// projectenddate
			$this->projectenddate->ViewValue = $this->projectenddate->CurrentValue;
			$this->projectenddate->ViewValue = ew_FormatDateTime($this->projectenddate->ViewValue, 5);
			$this->projectenddate->ViewCustomAttributes = "";

			// budget
			$this->budget->ViewValue = $this->budget->CurrentValue;
			$this->budget->ViewCustomAttributes = "";

			// projectname
			$this->projectname->LinkCustomAttributes = "";
			$this->projectname->HrefValue = "";
			$this->projectname->TooltipValue = "";

			// projectstartdate
			$this->projectstartdate->LinkCustomAttributes = "";
			$this->projectstartdate->HrefValue = "";
			$this->projectstartdate->TooltipValue = "";

			// projectenddate
			$this->projectenddate->LinkCustomAttributes = "";
			$this->projectenddate->HrefValue = "";
			$this->projectenddate->TooltipValue = "";

			// budget
			$this->budget->LinkCustomAttributes = "";
			$this->budget->HrefValue = "";
			$this->budget->TooltipValue = "";
		} elseif ($this->RowType == EW_ROWTYPE_ADD) { // Add row

			// projectname
			$this->projectname->EditAttrs["class"] = "form-control";
			$this->projectname->EditCustomAttributes = "";
			$this->projectname->EditValue = ew_HtmlEncode($this->projectname->CurrentValue);
			$this->projectname->PlaceHolder = ew_RemoveHtml($this->projectname->FldCaption());

			// projectstartdate
			$this->projectstartdate->EditAttrs["class"] = "form-control";
			$this->projectstartdate->EditCustomAttributes = "";
			$this->projectstartdate->EditValue = ew_HtmlEncode(ew_FormatDateTime($this->projectstartdate->CurrentValue, 5));
			$this->projectstartdate->PlaceHolder = ew_RemoveHtml($this->projectstartdate->FldCaption());

			// projectenddate
			$this->projectenddate->EditAttrs["class"] = "form-control";
			$this->projectenddate->EditCustomAttributes = "";
			$this->projectenddate->EditValue = ew_HtmlEncode(ew_FormatDateTime($this->projectenddate->CurrentValue, 5));
			$this->projectenddate->PlaceHolder = ew_RemoveHtml($this->projectenddate->FldCaption());

			// budget
			$this->budget->EditAttrs["class"] = "form-control";
			$this->budget->EditCustomAttributes = "";
			$this->budget->EditValue = ew_HtmlEncode($this->budget->CurrentValue);
			$this->budget->PlaceHolder = ew_RemoveHtml($this->budget->FldCaption());

			// Edit refer script
			// projectname

			$this->projectname->HrefValue = "";

			// projectstartdate
			$this->projectstartdate->HrefValue = "";

			// projectenddate
			$this->projectenddate->HrefValue = "";

			// budget
			$this->budget->HrefValue = "";
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
		if (!$this->projectname->FldIsDetailKey && !is_null($this->projectname->FormValue) && $this->projectname->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->projectname->FldCaption(), $this->projectname->ReqErrMsg));
		}
		if (!$this->projectstartdate->FldIsDetailKey && !is_null($this->projectstartdate->FormValue) && $this->projectstartdate->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->projectstartdate->FldCaption(), $this->projectstartdate->ReqErrMsg));
		}
		if (!ew_CheckDate($this->projectstartdate->FormValue)) {
			ew_AddMessage($gsFormError, $this->projectstartdate->FldErrMsg());
		}
		if (!$this->projectenddate->FldIsDetailKey && !is_null($this->projectenddate->FormValue) && $this->projectenddate->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->projectenddate->FldCaption(), $this->projectenddate->ReqErrMsg));
		}
		if (!ew_CheckDate($this->projectenddate->FormValue)) {
			ew_AddMessage($gsFormError, $this->projectenddate->FldErrMsg());
		}
		if (!$this->budget->FldIsDetailKey && !is_null($this->budget->FormValue) && $this->budget->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->budget->FldCaption(), $this->budget->ReqErrMsg));
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

		// projectname
		$this->projectname->SetDbValueDef($rsnew, $this->projectname->CurrentValue, "", FALSE);

		// projectstartdate
		$this->projectstartdate->SetDbValueDef($rsnew, ew_UnFormatDateTime($this->projectstartdate->CurrentValue, 5), ew_CurrentDate(), FALSE);

		// projectenddate
		$this->projectenddate->SetDbValueDef($rsnew, ew_UnFormatDateTime($this->projectenddate->CurrentValue, 5), ew_CurrentDate(), FALSE);

		// budget
		$this->budget->SetDbValueDef($rsnew, $this->budget->CurrentValue, "", FALSE);

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
			$this->projectid->setDbValue($conn->Insert_ID());
			$rsnew['projectid'] = $this->projectid->DbValue;
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
		$Breadcrumb->Add("list", $this->TableVar, "projectslist.php", "", $this->TableVar, TRUE);
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
if (!isset($projects_add)) $projects_add = new cprojects_add();

// Page init
$projects_add->Page_Init();

// Page main
$projects_add->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$projects_add->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var projects_add = new ew_Page("projects_add");
projects_add.PageID = "add"; // Page ID
var EW_PAGE_ID = projects_add.PageID; // For backward compatibility

// Form object
var fprojectsadd = new ew_Form("fprojectsadd");

// Validate form
fprojectsadd.Validate = function() {
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
			elm = this.GetElements("x" + infix + "_projectname");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $projects->projectname->FldCaption(), $projects->projectname->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_projectstartdate");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $projects->projectstartdate->FldCaption(), $projects->projectstartdate->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_projectstartdate");
			if (elm && !ew_CheckDate(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($projects->projectstartdate->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_projectenddate");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $projects->projectenddate->FldCaption(), $projects->projectenddate->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_projectenddate");
			if (elm && !ew_CheckDate(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($projects->projectenddate->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_budget");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $projects->budget->FldCaption(), $projects->budget->ReqErrMsg)) ?>");

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
fprojectsadd.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fprojectsadd.ValidateRequired = true;
<?php } else { ?>
fprojectsadd.ValidateRequired = false; 
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
<?php $projects_add->ShowPageHeader(); ?>
<?php
$projects_add->ShowMessage();
?>
<form name="fprojectsadd" id="fprojectsadd" class="form-horizontal ewForm ewAddForm" action="<?php echo ew_CurrentPage() ?>" method="post">
<?php if ($projects_add->CheckToken) { ?>
<input type="hidden" name="<?php echo EW_TOKEN_NAME ?>" value="<?php echo $projects_add->Token ?>">
<?php } ?>
<input type="hidden" name="t" value="projects">
<input type="hidden" name="a_add" id="a_add" value="A">
<div>
<?php if ($projects->projectname->Visible) { // projectname ?>
	<div id="r_projectname" class="form-group">
		<label id="elh_projects_projectname" for="x_projectname" class="col-sm-2 control-label ewLabel"><?php echo $projects->projectname->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $projects->projectname->CellAttributes() ?>>
<span id="el_projects_projectname">
<input type="text" data-field="x_projectname" name="x_projectname" id="x_projectname" size="30" maxlength="20" placeholder="<?php echo ew_HtmlEncode($projects->projectname->PlaceHolder) ?>" value="<?php echo $projects->projectname->EditValue ?>"<?php echo $projects->projectname->EditAttributes() ?>>
</span>
<?php echo $projects->projectname->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($projects->projectstartdate->Visible) { // projectstartdate ?>
	<div id="r_projectstartdate" class="form-group">
		<label id="elh_projects_projectstartdate" for="x_projectstartdate" class="col-sm-2 control-label ewLabel"><?php echo $projects->projectstartdate->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $projects->projectstartdate->CellAttributes() ?>>
<span id="el_projects_projectstartdate">
<input type="text" data-field="x_projectstartdate" name="x_projectstartdate" id="x_projectstartdate" placeholder="<?php echo ew_HtmlEncode($projects->projectstartdate->PlaceHolder) ?>" value="<?php echo $projects->projectstartdate->EditValue ?>"<?php echo $projects->projectstartdate->EditAttributes() ?>>
<?php if (!$projects->projectstartdate->ReadOnly && !$projects->projectstartdate->Disabled && !isset($projects->projectstartdate->EditAttrs["readonly"]) && !isset($projects->projectstartdate->EditAttrs["disabled"])) { ?>
<script type="text/javascript">
ew_CreateCalendar("fprojectsadd", "x_projectstartdate", "%Y/%m/%d");
</script>
<?php } ?>
</span>
<?php echo $projects->projectstartdate->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($projects->projectenddate->Visible) { // projectenddate ?>
	<div id="r_projectenddate" class="form-group">
		<label id="elh_projects_projectenddate" for="x_projectenddate" class="col-sm-2 control-label ewLabel"><?php echo $projects->projectenddate->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $projects->projectenddate->CellAttributes() ?>>
<span id="el_projects_projectenddate">
<input type="text" data-field="x_projectenddate" name="x_projectenddate" id="x_projectenddate" placeholder="<?php echo ew_HtmlEncode($projects->projectenddate->PlaceHolder) ?>" value="<?php echo $projects->projectenddate->EditValue ?>"<?php echo $projects->projectenddate->EditAttributes() ?>>
<?php if (!$projects->projectenddate->ReadOnly && !$projects->projectenddate->Disabled && !isset($projects->projectenddate->EditAttrs["readonly"]) && !isset($projects->projectenddate->EditAttrs["disabled"])) { ?>
<script type="text/javascript">
ew_CreateCalendar("fprojectsadd", "x_projectenddate", "%Y/%m/%d");
</script>
<?php } ?>
</span>
<?php echo $projects->projectenddate->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($projects->budget->Visible) { // budget ?>
	<div id="r_budget" class="form-group">
		<label id="elh_projects_budget" for="x_budget" class="col-sm-2 control-label ewLabel"><?php echo $projects->budget->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $projects->budget->CellAttributes() ?>>
<span id="el_projects_budget">
<input type="text" data-field="x_budget" name="x_budget" id="x_budget" size="30" maxlength="20" placeholder="<?php echo ew_HtmlEncode($projects->budget->PlaceHolder) ?>" value="<?php echo $projects->budget->EditValue ?>"<?php echo $projects->budget->EditAttributes() ?>>
</span>
<?php echo $projects->budget->CustomMsg ?></div></div>
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
fprojectsadd.Init();
</script>
<?php
$projects_add->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$projects_add->Page_Terminate();
?>
