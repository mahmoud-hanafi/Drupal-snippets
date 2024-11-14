/**
 * @file
 * Code OTP behaviors.
 */

(function ($, Drupal, once) {

  'use strict';

  /**
   * Behavior description.
   */
  Drupal.behaviors.codeOtp = {
    attach: function (context, settings) {

      function moveToNextInput($currentInput) {
        let $nextInput = $currentInput.next('.split-digit');
        if ($nextInput.length) {
          $nextInput.focus();
        }
      }

      $('.split-digits-container .split-digit', context).on('input', function () {
        // Check if the current input field has a single character.
        if ($(this).val().length === 1) {
          // Move focus to the next input field if it exists.
          $(this).next('.split-digit').focus();
        }
      });

      $('.split-digits-container .split-digit', context).on('keydown', function (e) {
        // Allow the user to go back with the backspace key
        if (e.key === "Backspace" && $(this).val().length === 0) {
          $(this).prev('.split-digit').focus();
        }
      });

      // Handle typing into the input fields.
      $('.split-digit', context).once('splitDigitsInput').on('input', function (e) {
        let $currentInput = $(this);
        let value = $currentInput.val();

        // If the user has typed a single digit, move to the next input.
        if (value.length === 1) {
          moveToNextInput($currentInput);
        }

        // Concatenate all digit values into the hidden field.
        let combinedValue = '';
        $('.split-digit', context).each(function () {
          combinedValue += $(this).val();
        });
        $('.full-number', context).val(combinedValue);
      });

      // Handle paste event to distribute pasted value across input fields.
      $('.split-digit', context).once('pasteEvent').on('paste', function (e) {
        e.preventDefault(); // Prevent the default paste behavior.

        // Get the pasted value.
        let paste = (e.originalEvent || e).clipboardData.getData('text');

        // Only process numeric input, up to 6 characters.
        if (/^\d{1,6}$/.test(paste)) {
          let digits = paste.split('');
          $('.split-digit', context).each(function (index) {
            $(this).val(digits[index] || ''); // Insert the digits.
            if (index < digits.length) {
              moveToNextInput($(this)); // Automatically move to the next field.
            }
          });

          // Update the hidden field with the full concatenated value.
          $('.full-number', context).val(paste);
        }
      });
      // $("#resend_otp_line").hide();
      Drupal.resendOtp.init(context);
      
      // Select all input fields with class 'split-digit' inside the container
      const inputs = $('.split-digits-container .split-digit');
      inputs.each(function(index) {
        $(this).on('input', function() {
          // Move to the next input field if one digit is entered
          if ($(this).val().length === 1 && index < inputs.length - 1) {
            inputs.eq(index + 1).focus();
          }
        });
        $(this).on('keydown', function(event) {
          // Move to the previous input field on Backspace if the current field is empty
          if (event.key === "Backspace" && $(this).val() === '' && index > 0) {
            inputs.eq(index - 1).focus();
          }
        });
      });
    }
  };
  Drupal.resendOtp = {
    init: function (context) {
      once('html', '#resend_otp_line', context).forEach(() => {
        // startTimer();
        $(document).on('click', '#resend_otp_line',function (e) {
          e.preventDefault();
          // $("#resend_otp_line").hide();
          // startTimer();
          $("#edit-otp").prop('disabled', false);
          $("#edit-submit").prop('disabled', false);
          const uid = $('input[name="uid"]').val();
          const email = $('input[name="email"]').val();
          console.log(uid, email);
          if (uid && email) {
            $.ajax({
              url: Drupal.url('otp-resend'), // The API endpoint
              method: "POST", // HTTP method: GET, POST, PUT, DELETE, etc.
              contentType: "application/json", // Set the content type (e.g., JSON)
              dataType: "json", // Expected response data type
              headers: {
                "Authorization": "Bearer your-token-here", // Add your custom headers here
                "Custom-Header": "YourCustomValue"
              },
              data: JSON.stringify({ uid: uid, email: email,}), // Data to be sent with the request
              success: function(response) {
                 console.log("Success:", response);
              },
              error: function(xhr, status, error) {
                console.error("Error:", status, error);
              }
            });
            // $("#resend").remove();
          }
        });     
      });
      function startTimer() {
        //Set the countdown duration (in seconds)
        let countdownDuration = 120;
        // Update the timer every second
        let countdownTimer = setInterval(function() {
        countdownDuration--;

        // Display the remaining time
        $('#timer').html(Drupal.t('Please wait ') + "<span style='color: var(--blue);'>" + countdownDuration + "</span>" + Drupal.t('secs to request another otp.'));

        // When countdown reaches zero, show the button and clear the interval
          if (countdownDuration <= 0) {
            clearInterval(countdownTimer);
            $('#timer').hide();
            $("#resend_otp_line").show();
            $("#edit-otp").prop('disabled', true);
            $("#edit-submit").prop('disabled', true);
            // $('#request-otp-btn').show(); // Show the button
          }
        }, 1000);
      }
    }
  }
} (jQuery, Drupal, once));
