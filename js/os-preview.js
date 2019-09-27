(function ($, Drupal) {
  'use strict';

  Drupal.behaviors.onesignalPreview = {
    attach: function (context, drupalSettings) {
      // Form elements.
      let titleInput = $('input[name="field_onesignal_title[0][value]"]', context);
      let messageeInput = $('textarea[name="field_onesignal_message[0][value]"]', context);

      // Win preview elements.
      let previewContainerWin = $('.windows');
      let winTitle = $('.fTyJsx', previewContainerWin);
      let winMessage = $('.gTFnUl', previewContainerWin);

      // MacOS preview elements.
      let previewContainerMac = $('.mac-os');
      let macTitle = $('.ioHjEQ', previewContainerMac);
      let macMessage = $('.kWfrHA', previewContainerMac);

      // Android preview elements.
      let previewContainerAndroid = $('.android');
      let androidTitle = $('.eHeOft', previewContainerAndroid);
      let androidMessage = $('.kogGHL', previewContainerAndroid);

      update();
      console.log(443534534)

      // Trigger update on elements change.
      titleInput.on('change keypress input', function () {
        update();
      });
      messageeInput.on('change keypress input', function () {
        update();
      });

      // Update all platforms previews.
      function update() {
        updateWin();
        updateMacOS();
        updateAndroid();
      }

      // Update Windows preview.
      function updateWin() {
        winTitle.text(titleInput.val());
        winMessage.text(messageeInput.val());
      }

      // Update MacOS preview.
      function updateMacOS() {
        macTitle.text(titleInput.val());
        macMessage.text(messageeInput.val());
      }

      // Update Android preview.
      function updateAndroid() {
        androidTitle.text(titleInput.val());
        androidMessage.text(messageeInput.val());
      }

      $('.android .eEvYbE').once('onesignalPreview').click(function () {
        if ($(this).hasClass('opened')) {
          $(this).removeClass('opened');
          $('.android .jMyxsl').removeClass('visible');
        }
        else {
          $(this).addClass('opened');
          $('.android .jMyxsl').addClass('visible');
        }
      });
    }
  };

  Drupal.behaviors.featuredImagePreview = {
    attach: function (context, drupalSettings) {
      let fImg = $('div[data-drupal-selector="edit-field-r-image-current"] img');
      let pImg = $('.image-preview-wrapper #preview-image img');
      if (fImg.length >= 1) {
        let imageURL = fImg["0"].attributes["0"].nodeValue;
        pImg["0"].attributes["0"].value = imageURL;
      }

      $(document).ajaxComplete(function () {
        let fImg = $('div[data-drupal-selector="edit-field-r-image-current"] img');
        if (fImg.length >= 1) {
          let imageURL = fImg["0"].attributes["0"].nodeValue;
          pImg["0"].attributes["0"].value = imageURL;

          $('.onesignal-wrapper .windows .krZqZq').attr('src', imageURL);
          $('.onesignal-wrapper .android .jMyxsl').attr('src', imageURL);
          $('.onesignal-wrapper .android .jMyxsl').addClass('visible');
          $('.onesignal-wrapper .android .eEvYbE').addClass('opened');
        }
      });
    }
  };

  Drupal.behaviors.osTitle = {
    attach: function (context, drupalSettings) {
      let title = $('input[name="title[0][value]"]');
      let osTitle = $('input[name="field_onesignal_title[0][value]"]');

      if (osTitle.val() == '') {
        osTitle.val(title.val().substring(0, 40));
      }

      title.on('change keypress input', function () {
        osTitle.val(title.val().substring(0, 40));
      });

      $('textarea[name = "field_onesignal_message[0][value]"]').attr('maxLength', 45);

      $('input[name="field_delivery_date[0][value][time]"]').timepicker({
        timeFormat: 'H:i:s',
        disableTimeRanges: true,
        show2400: true
      });
    }
  };

  Drupal.behaviors.requiredDelivery = {
    attach: function (context, drupalSettings) {
      let doNotSend = $('input[name="field_send_push[value]"]');
      let deliveryLabel = $('.field--name-field-onesignal-delivery .fieldset-legend');
      if (doNotSend.is(':checked')) {
        deliveryLabel.once().removeClass('form-required');
      }
      else {
        deliveryLabel.once().addClass('form-required');
      }
      doNotSend.once().change(function () {
        if (this.checked) {
          deliveryLabel.removeClass('form-required');
        }
        else {
          deliveryLabel.addClass('form-required');
        }
      });
    }
  };

}(jQuery, Drupal));
