<?php
// The Easy Ask Session
class EasyAsk_Impl_RemoteEasyAsk implements EasyAsk_iRemoteEasyAsk{
        // connection info
	private $m_sHostName  = "";
	private $m_nPort      = -1;

	private $m_sProtocol  = "http";
	private $m_sRootUri = "EasyAsk/apps/Advisor.jsp";

	private $m_options = null;
        
	// Creates the EasyAsk instance.
	function __construct($sHostName, $nPort, $dictionary){
            $this->m_sHostName = $sHostName;
            $this->m_nPort = $nPort;
            $this->m_options = new EasyAsk_Impl_Options($dictionary);
	}

	// Creates the generic URL for the website.
	private function formBaseURL(){
		$port = ($this->m_nPort || sizeof($this->m_nPort) > 0) ? ":" . $this->m_nPort : "";
		return $this->m_sProtocol . "://" . $this->m_sHostName . $port . "/" . $this->m_sRootUri . "?disp=json&oneshot=1&defarrangeby=///NONE///";//&defarrangeby=ARRANGE_BY_NONE";
	}
	
	// Converts a string parameter to a format usable by the website URL
	private function addParam($name, $val){
		return (null != $val && strlen($val) > 0) ? "&" . $name . "=" . $val : "";
	}
	
	// Converts a boolean parameter to a format usable by the website URL
	private function addTrueParam($name, $val){
		return $val ? "&" . $name . "=" . $val : "";
	}
	
	// Coverts a value without a name to a format usable by the website URL
	private function addNonNullVal($val){
		return $val != null ? $val : "";
	}

	// Creates a url for the current host settings and EasyAsk options
	private function formURL(){
		return $this->formBaseURL() . "&dct=" . $this->m_options->getDictionary() . "&indexed=1" . 
                "&ResultsPerPage=" . $this->m_options->getResultsPerPage() .
				$this->addParam("defsortcols", $this->m_options->getSortOrder()) .
				$this->addTrueParam("subcategories", $this->m_options->getSubCategories()) .
				$this->addTrueParam("rootprods", $this->m_options->getToplevelProducts()) .
				$this->addTrueParam("navigatehierarchy", $this->m_options->getNavigateHierarchy()) .
				$this->addTrueParam("returnskus", $this->m_options->getReturnSKUs()) .
				$this->addParam("defarrangeby", $this->m_options->getGrouping()) .
				$this->addParam("eap_GroupID", $this->m_options->getGroupId()) .
				$this->addParam("eap_CustomerID", $this->m_options->getCustomerId()) .
				$this->addParam("customer", $this->m_options->getCustomer()) .
				$this->addNonNullVal($this->m_options->getCallOutParam());
	}

	// User performs a search. Creates a URL based off of the search and then creates a RemoteResults and
    // loads the URL into it.
	function userSearch($path, $question){
		$url = $this->formURL() . "&RequestAction=advisor&CatPath=" . urlencode($path) . "&RequestData=CA_Search&q=" . urlencode($question);
		return $this->urlPost($url);
	}

	// User clicks on a category. Creates a URL based off of the action and then creates a RemoteResults and
    // loads the URL into it.
	function userCategoryClick($path, $cat){
		$pathToCat = ($path != null && strlen($path) > 0 ? ($path . "/") : "") . $cat; 
		$url = $this->formURL() . "&RequestAction=advisor&CatPath=" . urlencode($pathToCat) . "&RequestData=CA_CategoryExpand";
		return $this->urlPost($url);
	}

	// User clicks on a breadcrumb. Creates a URL based off of the action and then creates a RemoteResults and
    // loads the URL into it.
	function userBreadCrumbClick($path){
		$url = $this->formURL() . "&RequestAction=advisor&CatPath=" . urlencode($path) . "&RequestData=CA_BreadcrumbSelect";
		return $this->urlPost($url);
	}
	
	// User clicks on a attribute. Creates a URL based off of the action and then creates a RemoteResults and
    // loads the URL into it.
	function userAttributeClick($path, $attr){
		$url = $this->formURL() . "&RequestAction=advisor&CatPath=" . urlencode($path) . "&RequestData=CA_AttributeSelected&AttribSel=" . urlencode($attr);
		return $this->urlPost($url);
	}

	// User performs a page operation. Creates a URL based off of the action and then creates a RemoteRsults
    // instance and loads the URL into it.
	function userPageOp($path, $curPage, $pageOp){
		$url = $this->formURL() . "&RequestAction=navbar&CatPath=" . urlencode($path) . "&RequestData=" . urlencode($pageOp);
		if ($curPage != null && strlen($curPage) > 0){
			$url += "&currentpage=" . $curPage;
		}
		return $this->urlPost($url);
	}

	// User requests to go to a specific page. Creates a URL based off of the action and then creates a RemoteResults
    // instance and loads the URL into it.
	function userGoToPage($path, $pageNumber){
		$url = $this->formURL() . "&RequestAction=navbar&CatPath=" . urlencode($path) . "&RequestData=page" . $pageNumber;
		return $this->urlPost($url);
	}
	
	// Sets the protocol.  By default it is http.
	function setProtocol($protocol){
		$this->m_sProtocol = $protocol;
	}
	
	// Sets the EasyAsk options to an Options instance
	function setOptions($val){
		$this->m_options = $val;
	}
	
	// Gets the current EasyAsk Options
	function getOptions(){
		return $this->m_options;
	}
	
	// User Post does an http POST. Creates a RemoteResults instance and 
    // and Posts the URL to get results from the EasyAsk server.
	public function urlPost($url){
		echo $url;
		$res = new EasyAsk_Impl_RemoteResults();
		$res->load($url);
		return $res;
	}
}
?>