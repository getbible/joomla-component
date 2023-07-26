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




class DynamicFieldManager {
  constructor(fieldAId, fieldBId, defaultValue, fieldAValue, fieldBValue) {
    this.fieldA = jQuery('#' + fieldAId).chosen();
    this.fieldB = jQuery('#' + fieldBId).chosen();
    this.defaultValue = defaultValue;
    this.fieldAValue = fieldAValue;
    this.fieldBValue = fieldBValue;
    this.removedOption = null;
    this.fieldA.on('change', this.handleFieldAChange.bind(this));
    this.wordPlaceholders = document.querySelectorAll('.selected-word-placeholder');
  }

  handleFieldAChange() {
    if (this.fieldA.val() === this.fieldAValue) {
      this.wordPlaceholders.forEach(function(element) {
        element.style.display = 'none';
      });
      this.fieldB.find('option').each((index, option) => {
        if (option.value === this.fieldBValue) {
          this.removedOption = { value: option.value, text: option.text };
          jQuery(option).remove();
          if (this.fieldB.val() === this.fieldBValue) {
            this.headsup();
            this.fieldB.val(this.defaultValue);
          }
          this.fieldB.trigger('liszt:updated');
          this.fieldB.trigger('change');
        }
      });
    } else if (this.removedOption) {
      this.fieldB.append(new Option(this.removedOption.text, this.removedOption.value));
      this.removedOption = null;
      this.fieldB.trigger('liszt:updated');
      this.wordPlaceholders.forEach(function(element) {
        element.style.display = '';
      });
    }
  }
} 
