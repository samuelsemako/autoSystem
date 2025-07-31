let issueCount = 0;
function _actionModal(action) {
  if (action === "open") {
    $("#modal").css("display", "flex");
  } else if (action === "close") {
    $("#modal").css("display", "none").fadeOut(200);
  }
}

function _submitUser() {
  try {
    issueCount = 0;
  const fullName = $("#fullName").val().trim();
  const emailAddress = $("#emailAddress").val();
  const phoneNumber = $("#phoneNumber").val();
  const passport = $("#passport")[0].files[0];
  const userId = $("#searchUser").val(); // Determines if it's update or register

  $("#fullName, #emailAddress, #phoneNumber, #passport").removeClass("issue");
  $('#issue_fullName, #issue_emailAddress, #issue_phoneNumber, #issue_passport').html('');

  // === VALIDATIONS ===

  if (!fullName) {
    $("#fullName").addClass("issue");
    $('#issue_fullName').html('USER ERROR! Kindy provide full name to continue');
			issueCount++;
  }

  if (!emailAddress) {
    $("#emailAddress").addClass("issue");
    $('#issue_emailAddress').html('USER ERROR! Kindy provide email address to continue');
			issueCount++;
 
          
  } else if (!/\S+@\S+\.\S+/.test(emailAddress)) {
    $("#emailAddress").addClass("issue");
    $('#issue_emailAddress').html('USER ERROR! Input correct email format');
			issueCount++;
    
  }

  if (!phoneNumber) {
    $("#phoneNumber").addClass("issue");
    $('#issue_phoneNumber').html('USER ERROR! Input correct phone number');
			issueCount++;
  }

  if (!passport && !userId) {
    $("#passport").addClass("issue");
    _actionAlert("USER ERROR! Kindly upload a profile picture to continue", false);
  }

  if (issueCount>0){
			return;
		}

  // === FORM DATA ===
  const formData = new FormData();
  formData.append("fullName", fullName);
  formData.append("emailAddress", emailAddress);
  formData.append("phoneNumber", phoneNumber);

  if (passport) {
    formData.append("passport", passport);
  }

  const apiEndpint = userId
    ? `${endPoint}/auto_system/update-user?userId=${userId}`
    : `${endPoint}/auto_system/registration`;

   // === AJAX REQUEST ===
  $.ajax({
    type: "POST",
    url: apiEndpint,
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

      if (success === true) {
        _actionAlert(message, true);
        _clearFunction();
        _getSelectUser(fieldId)
      } else {
        _actionAlert(message, false);
      }
    },
    error: function (err) {
      _actionAlert(err.message, false);
    },
  });
} catch (error) {
  _actionAlert(error.message, false);
}
 
}

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
 
function _getSelectUser(fieldId) {
  try {
    $.ajax({
      type: "GET",
      url: endPoint + "/auto_system/fetchUsersRecord",
      dataType: "json",
      cache: false,
      headers: {
        apiKey: apiKey,
      },
      success: function (info) {
        const data = info.data;
        const success = info.success;

        if (success === true) {
          for (let i = 0; i < data.length; i++) {
            const id = data[i].userId;
            const value = data[i].fullName;
            $("#searchList_" + fieldId).append(
              "<li onclick=\"_clickOption('searchList_" +
                fieldId +
                "', '" +
                id +
                "', '" +
                value +
                "');\">" +
                value +
                "</li>"
            );
          }
        } else {
          _actionAlert(info.message, false);
        }
      },
    });
  } catch (error) {
    console.error("Error: ", error);
    _actionAlert("An unexpected error occurred. Please try again.", false);
  }
}

function _fetchUser() {
  const userId = $("#searchUser").val();
  $.ajax({
    type: "GET",
    url: endPoint + "/auto_system/fetchUsersRecord?userId=" + userId,
    dataType: "json",
    headers: {
      apiKey: apiKey,
    },
    success: function (info) {
      const fetch = info.data[0];
      const success = info.success;
      const message = info.message;
      if (success === true) {
        const fullName = fetch.fullName;
        const emailAddress = fetch.emailAddress;
        const phoneNumber = fetch.phoneNumber;
        const passport = fetch.passport;
        const documentStoragePath = fetch.documentStoragePath;

        $("#fullName").val(fullName);
        $("#emailAddress").val(emailAddress);
        $("#phoneNumber").val(phoneNumber);
        $("#userPixPreview").attr("src", documentStoragePath +"/" + passport);
      } else {
        _actionAlert(message, false);
      }
    },
    error: function () {
      _actionAlert(
        "An error occurred while processing your request! Please Try Again",
        false
      );
    },
  });
}

function _deleteUser() {
  const userId = $("#searchUser").val();

  $.ajax({
    type: "GET",
    url: endPoint + "/auto_system/delete-user?userId=" + userId,
    dataType: "json",
    headers: {
      apiKey: apiKey,
    },
    success: function (info) {
      const success = info.success;
      const message = info.message;

      if (success === true) {
        _clearFunction();
        _actionAlert(message, true);
      } else {
        _actionAlert(message, false);
      }
    },
  });
}

function _fetchUserRecords() {
  $.ajax({
    type: "GET",
    url: `${endPoint}/auto_system/fetchUsersRecord`,
    dataType: "json",
    headers: {
      apiKey: apiKey,
    },
    success: function (response) {
      const records = response.data;

      // Clear existing records
      $("#usersRecord").html("");

      let html = `
        <tr>
          <th>S/N</th>
          <th>User Id</th>
          <th>Full Name</th>
          <th>Email Address</th>
          <th>Phone Number</th>
          <th>Passport</th>
        </tr>
      `;
      $("#usersRecord").append(html);

      let sn = 1;
      let tableRow = "";
      records.forEach((record) => {
        tableRow += `
          <tr>
            <td>${sn++}</td>
            <td>${record.userId}</td>
            <td>${record.fullName}</td>
            <td>${record.emailAddress}</td>
            <td>${record.phoneNumber}</td>
            <td>
              ${
                record.passport
                  ? `<img src="${record.documentStoragePath +"/" + record.passport}" width="40" height="40" />`
                  : "No Image"
              }
            </td>
          </tr>
        `;
      });

      $("#usersRecord").append(tableRow);
    },
    error: function () {
      _actionAlert(
        "Failed to fetch All Records. Please try again later.",
        false
      );
    },
  });
}

function _clearFunction() {
  $("#fullName, #emailAddress, #phoneNumber").val("");
  $('#searchUser').val(" ");
  $("#userPixPreview").attr("src", "all-images/images/user.png");

  $('#fullName, #emailAddress, #phoneNumber, #passport').removeClass('issue');
    $('#issue_fullName, #issue_emailAddress, #issue_phoneNumber, #issue_passport').html('');
}

function _viewRecords(){
  _actionModal('open');
  _fetchUserRecords();
}