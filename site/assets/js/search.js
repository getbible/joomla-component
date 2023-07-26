/*----------------------------------------------------------------------------------|  io.vdm.dev  |----/
			Vast Development Method
/-------------------------------------------------------------------------------------------------------/

    @package    getBible.net

    @created    3rd December, 2015
    @author     Llewellyn van der Merwe <https://getbible.net>
    @git        Get Bible <https://git.vdm.dev/getBible>
    @github     Get Bible <https://github.com/getBible>
    @support    Get Bible <https://git.vdm.dev/getBible/support>
    @copyright  Copyright (C) 2015. All Rights Reserved
    @license    GNU/GPL Version 2 or later - http://www.gnu.org/licenses/gpl-2.0.html

/------------------------------------------------------------------------------------------------------*/

/* JS Document */
/**
 * JS Function to redirect to new search
 */
const handleSearch = async () => {
	try {
		// Make a request to your endpoint
		const response = await fetch(getSearchURL(searchField.value, searchFieldWord.value, searchFieldMatch.value, searchFieldCase.value, searchFieldTarget.value, searchFieldTranslation.value));

		// Wait for the server to return the response, then parse it as JSON.
		const data = await response.json();

		// Call another function after the response has been received
		if (data.url) {
			window.location.href = data.url;
		} else {
			// Handle any errors
			console.error("Error occurred: ", data);
		}
	} catch (error) {
		// Handle any errors
		console.error("Error occurred: ", error);
	}
};

/**
 * JS Function to get the current search URL
 */
const getSearchURL = (search, words = 1, match = 1, type_case = 1, target = 1000, translation = 'kjv') => {
	// build search url
	return urlAjax +
		'getSearchUrl&translation=' + urlencode(translation) +
		'&words=' + words +
		'&match=' + match +
		'&case=' + type_case +
		'&target=' + target +
		'&book=' + 0 +
		'&search=' + urlencode(search);
};

/**
 * JS Function to update the URL of the browser with the search query
 */
const updateUrlQuery = async () => {
	if (!urlCurrent.includes(urlSearch)) {
		// update the url query
		// window.history.pushState({}, '', urlSearch);
	}
};

/**
 * JS Function to redirect to new search
 */
const handleApp = async (book, chapter, verse, translation) => {
	try {
		// Make a request to your endpoint
		const response = await fetch(getAppURL(book, chapter, verse, translation));

		// Wait for the server to return the response, then parse it as JSON.
		const data = await response.json();

		// Call another function after the response has been received
		if (data.url) {
			window.location.href = data.url;
		} else {
			// Handle any errors
			console.error("Error occurred: ", data);
		}
	} catch (error) {
		// Handle any errors
		console.error("Error occurred: ", error);
	}
};

/**
 * JS Function to get the current search URL
 */
const getAppURL = (book, chapter, verse, translation = 'kjv') => {
	// build search url
	return urlAjax +
		'getAppUrl&translation=' + urlencode(translation) +
		'&book=' + book +
		'&chapter=' + chapter +
		'&verse=' + verse;
};

function urlencode (str) {
  //       discuss at: https://locutus.io/php/urlencode/
  //      original by: Philip Peterson
  //      improved by: Kevin van Zonneveld (https://kvz.io)
  //      improved by: Kevin van Zonneveld (https://kvz.io)
  //      improved by: Brett Zamir (https://brett-zamir.me)
  //      improved by: Lars Fischer
  //      improved by: Waldo Malqui Silva (https://fayr.us/waldo/)
  //         input by: AJ
  //         input by: travc
  //         input by: Brett Zamir (https://brett-zamir.me)
  //         input by: Ratheous
  //      bugfixed by: Kevin van Zonneveld (https://kvz.io)
  //      bugfixed by: Kevin van Zonneveld (https://kvz.io)
  //      bugfixed by: Joris
  // reimplemented by: Brett Zamir (https://brett-zamir.me)
  // reimplemented by: Brett Zamir (https://brett-zamir.me)
  //           note 1: This reflects PHP 5.3/6.0+ behavior
  //           note 1: Please be aware that this function
  //           note 1: expects to encode into UTF-8 encoded strings, as found on
  //           note 1: pages served as UTF-8
  //        example 1: urlencode('Kevin van Zonneveld!')
  //        returns 1: 'Kevin+van+Zonneveld%21'
  //        example 2: urlencode('https://kvz.io/')
  //        returns 2: 'https%3A%2F%2Fkvz.io%2F'
  //        example 3: urlencode('https://www.google.nl/search?q=Locutus&ie=utf-8')
  //        returns 3: 'https%3A%2F%2Fwww.google.nl%2Fsearch%3Fq%3DLocutus%26ie%3Dutf-8'
  str = (str + '')
  return encodeURIComponent(str)
    .replace(/!/g, '%21')
    .replace(/'/g, '%27')
    .replace(/\(/g, '%28')
    .replace(/\)/g, '%29')
    .replace(/\*/g, '%2A')
    .replace(/~/g, '%7E')
    .replace(/%20/g, '+')
}