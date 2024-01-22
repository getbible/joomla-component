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

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper as Html;
use Joomla\CMS\Layout\LayoutHelper;



?>
<p>At <a href="https://getbible.net/">getBible</a>, we've established a robust system to keep our API synchronized with the <a href="https://wiki.crosswire.org/">Crosswire</a> project's <a href="http://www.crosswire.org/sword/modules/ModDisp.jsp?modType=Bibles">modules</a>. Let me explain how this integration works in simple terms.</p>

<p>We source our Bible text directly from the <a href="https://wiki.crosswire.org/">Crosswire</a> <a href="http://www.crosswire.org/sword/modules/ModDisp.jsp?modType=Bibles">modules</a>. To monitor any updates, we generate "hash values" for each chapter, book, and translation. These hash values serve as unique identifiers that change only when the underlying content changes, thereby ensuring a tight integration between <a href="https://getbible.net/">getBible</a> and the <a href="https://wiki.crosswire.org/">Crosswire</a> modules.</p>

<p>Every month, an automated process runs for approximately three hours. During this window, we fetch the latest Bible text from the <a href="https://wiki.crosswire.org/">Crosswire</a> modules. Subsequently, we compare the new hash values and the text with the previous ones. Any detected changes trigger updates to both our <a href="https://git.vdm.dev/getBible/v2">official getBible hash repository</a> and the <a href="https://api.getbible.net">Bible API</a> for all affected <a href="https://api.getbible.net/v2/translations.json">translations</a>. This system has been operating seamlessly for several years.</p>

<p>Once the updates are complete, any application utilizing our <a href="https://api.getbible.net">Bible API</a> should monitor the <a href="https://getbible.net/docs#mapping-helpers">hash values</a> at the chapter, book, or translation level. Spotting a change in these values indicates that they should update their respective systems.</p>

<p>Hash values can change due to various reasons, including textual corrections like adding omitted verses, rectifying spelling errors, or addressing any discrepancies flagged by the publishers maintaining the <a href="http://www.crosswire.org/sword/modules/ModDisp.jsp?modType=Bibles">modules</a> at <a href="https://wiki.crosswire.org/">Crosswire</a>.</p>

<p>The <a href="https://wiki.crosswire.org/">Crosswire</a> initiative, also known as the SWORD Project, is the "source of truth" for <a href="https://wiki.crosswire.org/Frontends:getBible">getBible</a>. Any modifications in the <a href="https://wiki.crosswire.org/">Crosswire</a> <a href="http://www.crosswire.org/sword/modules/ModDisp.jsp?modType=Bibles">modules</a> get reflected in our API within days, ensuring our users access the most precise and current Bible text. We pledge to uphold this standard as long as <a href="https://getbible.net/">getBible</a> exists and our build scripts remain operational.</p>

<p>We're united in our mission to preserve the integrity and authenticity of the Bible text. If you have questions or require additional information, please use our <a href="https://git.vdm.dev/getBible/support">support system</a>. We're here to assist and will respond promptly.</p>

<p>Thank you for your understanding and for being an integral part of the <a href="https://getbible.net/">getBible</a> community.</p>
