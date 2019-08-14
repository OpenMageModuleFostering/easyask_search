<?php
// Serves as the xml document that holds search results
class EasyAsk_Impl_RemoteResults implements EasyAsk_iNavigateResults{
	private $m_doc;
	private $m_attrsInfo = null;
	private $m_catPath = null;
	private $m_itemDescriptions = null;
	private $m_items;
	private $m_bct = null;
	private $m_bHierarachyProcessed = false;
	private $m_navHier = null;
	private $m_commonAttributes = null;
	private $m_commentary = null;
	private $m_displayFormat = null;
	private $m_catInfo = null;
	private $m_groupSet = null;
	private $m_isGrouped = false;
	private $m_featuredProducts = null;
	private $m_carveOuts = null;
	private $m_arrangedByChoices = null;
	private $m_banners = null;

	// Creates a new instance.
	function __construct(){
		$this->m_doc = new DOMDocument();
	}

	// Loads a URL into the instance, then determines the appropriate results and layout.
	function load($url){
		$start = microtime(true);
		//$xml = file_get_contents($url);
		$xml = $this->url_get_contents($url);
		$xmltime = microtime(true);
		Mage::log("Profile Info: Time to get EasyAsk xml " . ($xmltime - $start));
        $this->m_doc = json_decode($xml);	    	
		$objecttime = microtime(true);
        Mage::log("Profile Info: Time to make EasyAsk xml object " . ($objecttime - $xmltime));
		$this->determineLayout();
	}
	
	function url_get_contents ($Url) {
		if (!function_exists('curl_init')){
			die('CURL is not installed!');
		}
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $Url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$output = curl_exec($ch);
		curl_close($ch);
		return $output;
	}

	// If there is a code xml node in the xmlDoc, returns the contained code.
	function getReturnCode(){
		$nodeRC = $this->m_doc->returnCode;
		if ($nodeRC >= 0){
			$rc = $nodeRC;
			return $rc;
		}
		return -1;
	}

	// If an error message currently exists in the RemoteRestults, returns it.
	function getErrorMsg(){
		$node = $this->m_doc->errorMsg;
		return null != $node ? $node : null;
	}

	// Returns the Search Engine Optimization path based off of the current bread crumb trail.
	function getCatPath(){
		$purePath = $this->m_doc->source->navPath->navPathNodeList[sizeof($this->m_doc->source->navPath->navPathNodeList) - 1]->purePath;
		return $this->m_catPath = ($purePath ? $purePath : "All Products");
	}

	function getPath(){
		return $this->getCatPath();
	}

	// Creates a new CategoriesInfo instance based off of the xml doc
	private function processCategories(){
		if ($this->m_catInfo == null){
			$this->m_catInfo = new EasyAsk_Impl_CategoriesInfo($this->m_doc->source);
		}
	}

	// Returns the current list of ResultCategory
	function getDetailedCategories($nDisplayMode){
		$this->processCategories();
		return $this->m_catInfo->getDetailedCategories($nDisplayMode);
	}

	// Returns the current list of ResultCategory
	function getDetailedCategoriesFull(){
		return $this->getDetailedCategories(0);
	}
	
	// Returns the initial list size for categories
	function getInitDisplayLimitForCategories(){
		$this->processCategories();
		return $this->m_catInfo->getInitDisplayLimitForCategories();
	}

	// Return a string for the suggested category name (common parent)
	function getSuggestedCategoryTitle(){
		$this->processCategories();
		return $this->m_catInfo->getSuggestedCategoryTitle();
	}

	function getSuggestedCategoryID(){
		$this->processCategories();
		return $this->m_catInfo->getSuggestedCategoryID();
	}

	// Creates an ItemDecriptions instance for the xmlDoc
	private function processItemDescriptions(){
		if ($this->m_itemDescriptions == null){
		    if (isset($this->m_doc->source->products)){
    			$node = $this->m_doc->source->products->itemDescription;
    			if ($node){
    				$this->m_itemDescriptions = new EasyAsk_Impl_ItemDescriptions($node);
    			}else{
    				$this->m_itemDescriptions = new EasyAsk_Impl_ItemDescriptions(null);
    			}
		    } else {
				$this->m_itemDescriptions = new EasyAsk_Impl_ItemDescriptions(null);
		    }
		}
	}

	// Returns an ItemDescriptions instance for the xmlDoc
	function getItemDescriptions() {
		$this->processItemDescriptions();
		return $this->m_itemDescriptions;
	}

	// Returns to total number of pages needed to hold the results of the INavigateResults.
	function getPageCount() { return $this->getItemDescriptions()->getPageCount(); }

	// Gets the index of the current page of results that the INavigateResults is displaying.
	function getCurrentPage() { return $this->getItemDescriptions()->getCurrentPage(); }
	function getIsDrillDown() { return $this->getItemDescriptions()->getIsDrillDown(); }

	// Gets the total number of items currently contained within the INavigateResults.
	function getTotalItems() { return $this->getItemDescriptions()->getTotalItems(); }

	// Returns the current number of results per page.
	function getResultsPerPage() { return $this->getItemDescriptions()->getResultsPerPage(); }

	// Returns the index of the first result item.
	function getFirstItem() { return $this->getItemDescriptions()->getFirstItem(); }

	// Returns the index of the last result item.
	function getLastItem() { return $this->getItemDescriptions()->getLastItem(); }

	// Returns the current sort order, if any, for the results
	function getSortOrder() { return $this->getItemDescriptions()->getSortOrder(); }

	// Returns a list of data descriptions for the xmlDoc
	function getDataDescriptions() { return $this->getItemDescriptions()->getDataDescriptions(); }
	function getResultCount() {return $this->getTotalItems();}

	// Creates a list of itemRows based off of the search.
	function processItems(){
		if ($this->m_items == null){
			$this->m_items = array();
			if (!$this->m_isGrouped){
                $items = $this->m_doc->source->products->items;
				if ($items){
				    foreach ($items as $item){
					    $this->m_items[] = new EasyAsk_Impl_ItemRow($this->getDataDescriptions(),$item);
                    }
				}
			}
		}
	}

	// Retrieves the data stored within an itemrow from the current page.
	function getCellData($row, $col){
		$this->processItems();
		$adjust = ($this->getCurrentPage()-1)*$this->getResultsPerPage();
		return $this->m_items[$row - $adjust]->getFormattedText($col);
	}

	// Returns the index of a column contained within the ItemDescriptions
	function getColumnIndex($colName) {
		return $this->getItemDescriptions()->getColumnIndex($colName);
	}

	// Processes the bread crumb trail for the current search.
	function processBreadCrumbTrail(){
		$node = $this->m_doc->source->navPath;
		if ($node){
			$this->m_bct = new EasyAsk_Impl_BreadCrumbTrail($node);
		}else{
			$this->m_bct = new EasyAsk_Impl_BreadCrumbTrail(null);
		}
	}

	// Returns the bread crumb trail for the current search.
	function getBreadCrumbTrail() {
		$this->processBreadCrumbTrail();
		return $this->m_bct;
	}

	// Whether the use is currently looking at the top level of the search
	function getAtTopNode() {
		return $this->m_doc->source->atTopNode;
	}

	function getProductsFromGlobalSearch() {
		return $this->m_doc->source->productsFromGlobalSearch;
	}

	// Returns true if products could not be found in the current context, but were found by modifying the user query.
	function getItemsFoundByModifyingQuery() {
		return $this->m_doc->source->itemsFoundByModifyingQuery;
	}

	// Returns true if products/items were found through a secondary search.
	function getItemsFoundWIthSecondarySearch() {
		return $this->m_doc->source->itemsFoundWithSecondarySearch;
	}

	// Returns the method in which the product listing was obtained.
	function getProductRetrievalMethod() {
		return $this->m_doc->source->productRetrievalMethod;
	}

	// Returns the method in which the attribute listing was obtained.
	function getAttributeRetrievalMethod() {
		return $this->m_doc->source->attributeRetrievalMethod;
	}

	function getQuestion() {
		return $this->m_doc->source->question;
	}

	function getOriginalQuestion() {
		return isset($this->m_doc->source->originalQuestion)?$this->m_doc->source->originalQuestion:'';
	}

	function getIsCommand() {
		return $this->m_doc->source->question->isCommand;
	}

	// Processes a NavigateHierarchy based off of the xmlDoc
	function processNavigateHierarchy(){
		if (!$this->m_bHierarachyProcessed){
			$hier = $this->m_doc->source->navigateHierarchy->navHierNode;
			if ($hier){
				$this->m_navHier = new EasyAsk_Impl_NavigateHierarchy($hier);
			}
			$this->m_bHierarachyProcessed = true;
		}
	}

	// Returns the current NavigateHierarchy for the search.
	function getNavigateHierarchy() {
		$this->processNavigateHierarchy();
		return $this->m_navHier;
	}

	// Processes the attributes into an AttributeInfo for the current search.
	function processAttributes(){
		if ($this->m_attrsInfo == null){
			$this->m_attrsInfo = isset($this->m_doc->source->attributes)? new EasyAsk_Impl_AttributesInfo($this->m_doc->source->attributes):new EasyAsk_Impl_AttributesInfo(null);
		}
	}

	// Returns the AttributeInfor for the current search based off of the xmlDoc.
	function getAttributeInfo($attrNode){
		$results = array();
		if ($attrNode){
			$attrs = $attrNode->attribute;
			foreach ($attrs as $attr){
				$results[] = new EasyAsk_Impl_AttributeInfo($attr);
			}
		}
		return $results;
	}

	// Creates an AttributeInfo for
	function processCommonAttributes(){
		if ($this->m_commonAttributes == null){
			$this->m_commonAttributes = isset($this->m_doc->source->commonAttribute)? new EasyAsk_Impl_AttributesInfo($this->m_doc->source->commonAttribute):new EasyAsk_Impl_AttributesInfo(null);
		}
	}

	// Returns whether the Initial Display settings is limited against displaying a certain attribute.
	function isInitialDispLimitedForAttrNames() {
		$this->processAttributes();
		return $this->m_attrsInfo->isInitialDispLimitedForAttrNames();
	}

	// Returns the initial display mode for an attribute's value
	function getInitialDispLimitForAttrNames() {
		$this->processAttributes();
		return $this->m_attrsInfo->getInitialDispLimitForAttrNames();
	}

	function getInitialDisplayList($attrType) {
		$this->processAttributes();
		return $this->m_attrsInfo->getInitialDisplayList($attrType);
	}

	// Returns a list of attribute names of the specified type.
	function getAttributeNames($attrFilter, $displayMode){
		$this->processAttributes();
		return $this->m_attrsInfo->getAttributeNames($attrFilter, $displayMode);
	}

	// Returns a vector of NavigateAttribute objects for the specified attribute name for a specified group.
	// NavigateAttribute objects contain additional information about each attribute value.
	function isInitialDispLimitedForAttrValues($attrName){
		$this->processAttributes();
		return $this->m_attrsInfo->isInitialDispLimitedForAttrValues($attrName);
	}

	// Returns the initial display mode for an attribute's value
	function getInitialDispLimitForAttrValues($attrName){
		$this->processAttributes();
		return $this->m_attrsInfo->getInitialDispLimitForAttrValues($attrName);
	}
	
	// Returns if the attribute is a range filter
	public function isRangeFilter($attrName){
		$this->processAttributes();
		return $this->m_attrsInfo->isRangeFilter($attrName);
	}

	// Returns a vector of NavigateAttribute objects for the specified attribute name for a specified group.
	// NavigateAttribute objects contain additional information about each attribute value.
	function getDetailedAttributeValues($attrName, $displayMode){
		$this->processAttributes();
		return $this->m_attrsInfo->getDetailedAttributeValues($attrName, $displayMode);
	}

	// Returns a list of all attribute names in the current search
	public function getAttributeNamesFull(){
		return $this->getAttributeNames(1, 0);
	}

	// Returns a list of NavigateAttribute objects for the specified attribute name.
	// NavigateAttribute objects contain additional information about each attribute value.
	// Returns the full list.
	function getDetailedAttributeValuesFull($attrName){
		$this->processAttributes();
		return $this->m_attrsInfo->getDetailedAttributeValues($attrName, 0);
	}

	// Returns a corresponding AttributeInfo instance for an attribute
	function getCommonAttrInfo($attrName){
		$this->processCommonAttributes();
		if (isset($this->m_commonAttributes)){
		    return $this->m_commonAttributes->getDetailedAttributeValues($attrName, 0);
		}
		return null;
	}

	private $NODE_ATTRIB_SELECT = "////AttribSelect=";
	private $splitValSep = ";;;;";

	// Returns whehter an attribute was selected by the user.
	function wasAttributeSelected($attrName){
		foreach ($this->getBreadCrumbTrail()->getSearchPath() as $node){
			if (2 == $node->getType()){
				$path = $node->getPath();
				$idx = strpos($path, $this->NODE_ATTRIB_SELECT);
				if (0 <= $idx){
					//					$vals = path.Substring(idx + NODE_ATTRIB_SELECT.Length).Split(splitValSep, StringSplitOptions.None);
					$vals = explode($this->splitValSep, substr($path, $idx + sizeof($this->NODE_ATTRIB_SELECT)));
					for ($i = 0; $i < sizeof($vals); $i++){
						if (stripos($vals[$i], $attrName . " = '") == 0){
							return true;
						}
					}
				}
			}
		}
		return false;
	}

	// Returns a vector of the attribute names that are common to the results and normally not displayed.
	// The combination of getAttributeNames and getCommonAttributeNames covers all the attributes for the set.
	function getCommonAttributeNames($onlySelected){
		$this->processCommonAttributes();
		$results = array();

		return $this->m_commonAttributes->getAttributeNames(1,0);
	}

	// Returns a list of NavigateAttribute objects for the specified common attribute name.
	// NavigateAttribute objects conatin additional information about each attribute value.
	function getDetailedCommonAttributeValues($attrName, $displayMode){
//		$attrInfo = $this->getCommonAttrInfo($attrName);
//		if ($attrInfo){
//			if (1 == $displayMode && !($attrInfo->getIsLimited())){
//				$displayMode = 0;
//			}
//			return 0 == $displayMode ? $attrInfo->getFullList() : $attrInfo->getInitialList();
//		}
//		return array();
		$this->processCommonAttributes();
	    if (isset($this->m_commonAttributes)){
		    return $this->m_commonAttributes->getDetailedAttributeValues($attrName, 0);
		}
		return null;
	    
	}

	// Returns a list of NavigateAttribute objects for the specified common attribute name.
	// NavigateAttribute objects conatin additional information about each attribute value.
	function getDetailedCommonAttributeValuesFull($attrName){
		return $this->getDetailedCommonAttributeValues($attrName, 0);
	}

	function getCommentary(){
		if (!$this->m_commentary){
			$node = isset($this->m_doc->source->commentary)?$this->m_doc->source->commentary:'';
			$this->m_commentary = $node;
		}
		return $this->m_commentary;
	}

	function splitCommentary($key, $end){
		$commentary = $this->getCommentary();
		$result = "";
		$idx = strpos($commentary, $key);
		if ($idx !== false){
			$result = substr($commentary, $idx + sizeof(key));
			$idx = strpos($result, $end);
			if ($idx !== false){
				$result = substr($result, 0, $idx);
			}
		}
		return $result;
	}

	private $SPELL_CORRECTION_PREFACE = "Corrected Words:";
	private $COMMENTARY_SECTION_END = ';';

	// Gets suggested spell corrections for the current search terms
	function getSpellCorrections() {
		return $this->splitCommentary($this->SPELL_CORRECTION_PREFACE,$this->COMMENTARY_SECTION_END);
	}

	private $LIST_SEP = ',';
	private $CORRECTION_SEP = " is ";

	// Gets a list of any words that were corrected in the search
	function getCorrectedWords(){
		$spells = explode($this->LIST_SEP, $this->getSpellCorrections());
		$results = array();
		for($i = 0; $i < sizeof(spells); $i++){
			$parts = explode($spells[$i], $this->CORRECTION_SEP);
			$results[] = trim($parts[0]);
		}
		return $results;
	}

	// Checks for a correction for a search word. Will return null if there are no corrections.
	function getCorrection($word){
		$spells = explode($this->getSpellCorrections(),$this->LIST_SEP);
		for($i = 0; $i < sizeof($spells); $i++){
			$parts = explode($spells[$i], $this->CORRECTION_SEP);
			if (0 == strcmp(trim($parts[0]),$word)){
				return trim($parts[1]);
			}
		}
		return null;
	}

	private $RELAXATION_PREFACE = "Ignored:";

	function getRelaxedTerms(){
		$terms = explode($this->splitCommentary($this->RELAXATION_PREFACE,$this->COMMENTARY_SECTION_END), $this->LIST_SEP);
		$results = array();
		for($i = 0; $i < sizeof($terms); $i++){
			$results[] = trim($terms[$i]);
		}
		return $results;
	}

	// Sets the RemoteResult display format to that contained within the xmlDoc
	function processDisplayFormat(){
		if (!$this->m_displayFormat){
			$this->m_displayFormat = $this->getReturnCode() === 0 ? new EasyAsk_Impl_DisplayFormat($this->m_doc->source->displayFormat) : new EasyAsk_Impl_DisplayFormat($this->m_doc->displayFormat);
		}
	}

	function isPresentationError() {
		$this->processDisplayFormat();
		return !$this->m_displayFormat ? false : $this->m_displayFormat->isPresentationError();
	}

	function isRedirect() {
		$this->processDisplayFormat();
		return !$this->m_displayFormat ? false : $this->m_displayFormat->isRedirect();
	}

	function getRedirect() {
		$this->processDisplayFormat();
		return $this->isRedirect() ? $this->getErrorMsg() : null;
	}

	private function getFirstChild($node){
		foreach ($node->children() as $child){
			if($child){
				return $child;
			}
		}
		return null;
	}

	// Figures the layout of the results. How to group them, etc.
	public function determineLayout(){
		$this->m_isGrouped = !empty($this->m_doc->source->products->groups);
	}

	// Is this RemoteResult a GroupedResult
	public function isGroupedResult(){
		return $this->m_isGrouped;
	}

	// Creates a GroupedSetInfo instance for the current instance.
	public function processGroups(){
		if ($this->m_groupSet == null && $this->m_isGrouped){
			$this->m_groupSet = new EasyAsk_Impl_GroupedSetInfo($node, $res);
		}
	}

	// Returns a GroupedSetInfo for the current search results
	public function getGroupedResult(){
		$this->processGroups();
		return $this->m_groupSet;
	}

	// Returns an ItemRow from the currently displayed page
	public function getRow($pageRow){
		$this->processItems();
		return $this->m_items[$pageRow];
	}

	// Returns a list of carveout objects for the current search result
	public function getCarveOuts(){
		if ($this->m_carveOuts == null){
			$this->m_carveOuts = array();
			foreach ($this->m_doc->source->carveOuts as $carveOut){
				$this->m_carveOuts[] = new EasyAsk_Impl_CarveOut($this, $carveOut);
			}
		}
		return $this->m_carveOuts;
	}

	// Returns a ResultsRowGroup that contains the featured products. Null means none.
	public function getFeaturedProducts(){
		if ($this->m_featuredProducts == null){
			$this->m_featuredProducts = new EasyAsk_Impl_FeaturedProducts($this, $this->m_doc->source->featuredProducts);
		}
		return $this->m_featuredProducts;
	}

	// Returns a list of possible arrange by by choices. The result set can be arranged by one of these choices.
	// The value GroupedResultSet.GROUP_NO_GROUPING is returned as a choice for not grouping.
	public function getArrangeByChoices(){
		if($this->m_arrangedByChoices == null){
			$this->m_arrangedByChoices = array();
			$cats = $this->getDetailedCategoriesFull();
			if (sizeof($cats) > 0){
				$this->m_arrangedByChoices[] = "Category";
			}
			$attrNames = $this->getAttributeNamesFull();
			foreach($attrNames as $name){
				$vals = $this->getDetailedAttributeValuesFull($name);
				if (sizeof($vals) > 1){
					$this->m_arrangedByChoices[] = $name;
				}else if (sizeof($vals) == 1 && $vals[0]->getProductCount() < $this->getTotalItems()){
					$this->m_arrangedByChoices[] = $name;
				}
			}
		}
		return $this->m_arrangedByChoices;
	}
	
	// Returns the original question asked.
	public function getOriginalQuestionAsked(){
	    $originalQuestionAsked = '';
	    $searchPath = $this->getBreadCrumbTrail()->getSearchPath();
	    foreach ($searchPath as $path){
	        if ($path->getType() === 3){
	            $originalQuestionAsked = $path->getValue();
	        }
	    }
	    return $originalQuestionAsked;
	}
	
	// Processes the banners into an Banners for the current search.
	private function processBanners(){
		if ($this->m_banners == null){
			$this->m_banners = isset($this->m_doc->source->banners)? new EasyAsk_Impl_Banners($this->m_doc->source->banners) : null;
		}
	}

	// Returns if there is a banner associated with this Category/Attribute 
	public function hasBanner($type){
		$this->processBanners();
		return isset($this->m_banners) ? $this->m_banners->hasBanner($type) : false;
	}
	
	//Return Banner Information
	public function getBanner($type){
		$this->processBanners();
		return isset($this->m_banners) ? $this->m_banners->getBanner($type) : null;
	}
}
?>
