document.addEventListener("DOMContentLoaded", function () {
  const accountItems = document.querySelectorAll(".accountitem");
  const modal = document.getElementById("requestModal");
  const addaccmodal = document.getElementById("addAccountModal");
  const addaccbutton = document.getElementById("addAccountButton");
  const modalName = document.getElementById("modalName");
  const modalUsername = document.getElementById("modalUsername");
  const modalEmail = document.getElementById("modalEmail");
  const modalContact = document.getElementById("modalContact");
  const modalUserID = document.getElementById("accountid");
  const modalUserIDDis= document.getElementById("modalAccountId");
  const closeButtons = document.querySelectorAll(".close");

  accountItems.forEach((item) => {
    item.addEventListener("click", function () {
      const name = this.getAttribute("data-name");
      const email = this.getAttribute("data-email");
      const contact = this.getAttribute("data-contact");
      const username = this.getAttribute("data-username");
      const userid = this.getAttribute("data-userid");
      
      modalUserID.value = userid;
      modalUserIDDis.value = userid;
      modalName.textContent = name;
      modalUsername.textContent = username;
      modalEmail.textContent = email;
      modalContact.textContent = contact;

      modal.style.display = "block";
    });
  });

  addaccbutton.addEventListener("click", function () {
    addaccmodal.style.display = "block";
  });

  closeButtons.forEach((button) => {
    button.addEventListener("click", function () {
      modal.style.display = "none";
      addaccmodal.style.display = "none";
    });
  });

  window.addEventListener("click", function (event) {
    if (event.target == modal || event.target == addaccmodal) {
      modal.style.display = "none";
      addaccmodal.style.display = "none";
    }
  });
});

document.addEventListener("DOMContentLoaded", function () {
  const tabButtons = document.querySelectorAll(".tabbutton");
  const accountItems = document.querySelectorAll(".accountitem");
  const addAccountButton = document.querySelector(".addaccount");
  const button1 = document.querySelector(".btn1");
  const button2 = document.querySelector(".btn2");

  // Handle Add Account Form Submission
  document
    .getElementById("addAccountForm")
    .addEventListener("submit", function (event) {
      event.preventDefault();

      const formData = new FormData(this);

      fetch("../../controllers/accountcontroller.php", {
        method: "POST",
        body: formData,
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.status === "success") {
            Swal.fire({
              icon: "success",
              html:
                '<p style="font-size: 24px; font-weight: bold;">' +
                data.message +
                "</p>",
              showConfirmButton: false,
              timer: 3000,
            }).then(() => {
              location.reload();
            });
          } else {
            Swal.fire({
              icon: "error",
              html:
                '<p style="font-size: 24px; font-weight: bold;">' +
                data.message +
                "</p>",
              showConfirmButton: false,
              timer: 3000,
            });
          }
        })
        .catch((error) => {
          Swal.fire({
            icon: "error",
            html: '<p style="font-size: 24px; font-weight: bold;">Something went wrong. Please try again later.</p>',
            showConfirmButton: false,
            timer: 3000,
          });
        });
    });

  tabButtons.forEach((button) => {
    button.addEventListener("click", function () {
      tabButtons.forEach((btn) => btn.classList.remove("active"));
      this.classList.add("active");

      const userType = this.classList[0];

      accountItems.forEach((item) => {
        if (item.getAttribute("data-usertype") === userType) {
          item.style.display = "block";
        } else {
          item.style.display = "none";
        }
      });

      if (userType !== "emp") {
        addAccountButton.style.display = "none";
        if (userType === "mngr") {
          button1.style.display = "none";
          button2.style.display = "none";
        }
        if (userType === "trst") {
          button1.style.display = "block";
          button2.style.display = "block";
          button2.textContent = "Delete";
        }
      } else {
        addAccountButton.style.display = "block";
        button1.style.display = "block";
        button2.style.display = "block";
        button2.textContent = "Disable";
      }
    });
  });
  document.querySelector(".tabbutton.active").click();
});

document.querySelectorAll(".accountitem").forEach((item) => {
    // Attach an event listener for the Edit button (this could be inside your detail modal)
    item.querySelector(".btn1").addEventListener("click", function(e) {
      e.stopPropagation(); // Prevent other click events if needed
  
      // Build the accountData object from data attributes on the item
      const accountData = {
        id: item.getAttribute("data-id"),
        name: item.getAttribute("data-name"),
        username: item.getAttribute("data-username"),
        email: item.getAttribute("data-email"),
        contactnum: item.getAttribute("data-contact")
      };
  
      // Call the function to open the modal with these details
      openEditModal(accountData);
    });
  });

function filterAccounts() {
  var input, filter, grid, items, name, i, txtValue, noAccountFound;
  input = document.getElementById("searchInput");
  filter = input.value.toUpperCase();
  grid = document.querySelector(".grid");
  items = grid.getElementsByClassName("accountitem");
  noAccountFound = true;

  for (i = 0; i < items.length; i++) {
    name = items[i].getAttribute("data-name");
    if (name) {
      txtValue = name;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        items[i].style.display = "";
        noAccountFound = false;
      } else {
        items[i].style.display = "none";
      }
    }
  }

  if (noAccountFound) {
    document.getElementById("noAccountFound").style.display = "block";
  } else {
    document.getElementById("noAccountFound").style.display = "none";
  }
}

function openEditModal(accountData) {
    document.getElementById("accountid").value = accountData.id || '';
    document.getElementById("name").value = accountData.name || '';
    document.getElementById("username").value = accountData.username || '';
    document.getElementById("email").value = accountData.email || '';
    document.getElementById("contactnum").value = accountData.contactnum || '';
    document.getElementById("password").value = "";

    document.getElementById("name").required = false;
    document.getElementById("username").required = false;
    document.getElementById("email").required = false;
    document.getElementById("contactnum").required = false;
    document.getElementById("password").required = false;

    
    // Change form state for edit mode
    document.querySelector("#addAccountModal .modal-title").textContent = "Edit Employee Account";
    document.querySelector("#addAccountModal .btn1").textContent = "Update";
    
    // Display the addAccountModal for editing
    document.getElementById("addAccountModal").style.display = "block";
  }
  

  function openEditModalFromModal() {
    const accountData = {
      id: document.getElementById("accountid").value,
      name: document.getElementById("modalName").textContent,
      username: document.getElementById("modalUsername").textContent,
      email: document.getElementById("modalEmail").textContent,
      contactnum: document.getElementById("modalContact").textContent
    };
    document.getElementById("name").required = false;
    document.getElementById("username").required = false;
    document.getElementById("email").required = false;
    document.getElementById("contactnum").required = false;
    document.getElementById("password").required = false;
    document.getElementById("action").value = "editEmpAccount";

    document.getElementById("addAccountModal").style.display = "block";
  }