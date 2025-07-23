function _actionModal(action) {
  if (action === "open") {
    $("#modal").css("display", "flex");
  } else if (action === "close") {
    $("#modal").css("display", "none").fadeOut(200);
  }
}

function _registerUser() {
  const fullName = $("#fullName").val().trim();
  const emailAddress = $("#emailAddress");
  const phoneNumber = $("#phoneNumber").val();
  const passport = $('#passport')[0].files[0];

  $("#fullName, #emailAddress, #phoneNumber, #passport").removeClass("issue");

  // === VALIDATIONS ===
  if (!fullName) {
    $("#fullName").addClass("issue");
    _actionAlert("USER ERROR! Kindly provide fullname to continue", false);
    return;
  }

  const nameRegex = /^[A-Za-z\s]+$/;

  if (!emailAddress) {
    $("#emailAddress").addClass("issue");
    _actionAlert("Provide email address to continue", false);
    return;
  }

  if (!phoneNumber) {
    $("#phoneNumber").addClass("issue");
    _actionAlert("Provide phone to continue", false);
    return;
  }

  const formData = new FormData();
  formData.append("fullName", fullName);
  formData.append("emailAddress", emailAddress);
  formData.append("phoneNumber", phoneNumber);
  formData.append("passport", passport);

  $.ajax({
    type: "POST",
    url: endPoint + "/auto_system/registeration",
    data: formData,
    dataType: "json",
    contentType: false,
    cache: false,
    processData: false,
    headers: {
      apiKey: apiKey,
    },
    success: function (info) {
      const success = info.success;
      const message = info.message;

      if (success === true) _actionAlert(message, true), _clearFunction();
      else _actionAlert(message, false);
    },
    error: function () {
      _actionAlert(
        "An error occurred while processing your request! Please Try Again",
        false
      );
    },
  });
}

/// Trigger File Upload function ///
$(function () {
  userPixPreview = {
    UpdatePreview: function (obj) {
      if (!window.FileReader) {
        // Handle browsers that don't support FileReader
        console.error("FileReader is not supported.");
      } else {
        var reader = new FileReader();

        reader.onload = function (e) {
          $("#userPixPreview").prop("src", e.target.result);
        };
        reader.readAsDataURL(obj.files[0]);
      }
    },
  };
});

function _clearFunction() {
  $("#fullName, #emailAddress, #phoneNumber, #passport").val("");
}
