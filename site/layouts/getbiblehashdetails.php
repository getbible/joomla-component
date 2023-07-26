<?php
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

// No direct access to this file
defined('JPATH_BASE') or die('Restricted access');



?>
<p>We, at <a href="https://getbible.net/">getBible</a>, have a robust system to ensure our API stays up-to-date with the <a href="http://www.crosswire.org/">Crosswire</a> project's <a href="http://www.crosswire.org/sword/modules/ModDisp.jsp?modType=Bibles">modules</a>. Let me walk you through how it all works, making sure it all makes sense without any technical jargon.</p>

<p>Our Bible text comes directly from the <a href="http://www.crosswire.org/">Crosswire</a> <a href="http://www.crosswire.org/sword/modules/ModDisp.jsp?modType=Bibles">modules</a>. To track any changes, we create what are known as "hash values" for every chapter, book, and translation. Think of these hash values as unique identifiers that change when the content they're associated with changes. This system firmly links <a href="https://getbible.net/">getBible</a> with the <a href="http://www.crosswire.org/">Crosswire</a> <a href="http://www.crosswire.org/sword/modules/ModDisp.jsp?modType=Bibles">modules</a>.</p>

<p>Every month, a process kicks off that takes about three hours. During this time, we pull the Bible text again from the <a href="http://www.crosswire.org/">Crosswire</a> <a href="http://www.crosswire.org/sword/modules/ModDisp.jsp?modType=Bibles">modules</a> and place it into a special repository. We then check if there are any changes by comparing the new hash values and the actual text with the old ones. If we detect any changes, we automatically update our official <a href="https://getbible.net/">getBible</a> repository, which holds the JSON <a href="https://github.com/getbible/v2#api-usage">API</a> files. This process has been running flawlessly for nearly three years and happens without any human intervention.</p>

<p>Once our official repository is updated, a new event is triggered to push these fresh files to our actual <a href="https://github.com/getbible/v2#api-usage">API</a> endpoints, which is <code>https://api.getbible.net/v2/translations.json</code>. This URL lists all the available translations in our API.</p>

<p>At this point, all the hash values are updated too. So, any application using our <a href="https://github.com/getbible/v2#api-usage">API</a> should monitor for changes in these hash values, either on the chapter, book, or translation level. If a change in hash is detected, they should update their systems accordingly.</p>

<p>Changes in hash values may occur due to fixes in the text, such as adding missing verses or correcting spelling mistakes, or any other types of errors detected by the Publishers maintaining the <a href="http://www.crosswire.org/sword/modules/ModDisp.jsp?modType=Bibles">modules</a> at <a href="http://www.crosswire.org/">Crosswire</a>.</p>

<p>The <a href="http://www.crosswire.org/">Crosswire</a> project, also known as the SWORD Project, is therefore the "source of truth" for <a href="https://getbible.net/">getBible</a>. Any change in the <a href="http://www.crosswire.org/">Crosswire</a> <a href="http://www.crosswire.org/sword/modules/ModDisp.jsp?modType=Bibles">modules</a> reflects in our API within a few days, ensuring our API always provides the most accurate and up-to-date Bible text. This will continue for as long as <a href="https://getbible.net/">getBible</a> exists and we're able to run our build scripts.</p>

<p>Remember, we're all in this together to maintain the integrity and accuracy of the Bible text. So, if you have any questions or need further clarification, please feel free to open issues in the relevant repositories, and we'll respond as soon as we can.</p>

<p>Thank you very much for your attention and for being a part of our mission at <a href="https://getbible.net/">getBible</a>.</p>
