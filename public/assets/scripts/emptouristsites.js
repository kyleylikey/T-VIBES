function bitmaskToDays(bitmaskStr) {
    const days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thurs', 'Fri', 'Sat'];
    let result = [];
    
    // Convert binary string to integer
    let bitmask = parseInt(bitmaskStr, 2);

    for (let i = 0; i < days.length; i++) {
        if (bitmask & (1 << (6 - i))) { // Adjust the bit order
            result.push(days[i]);
        }
    }

    return result.join(', ');
}

function daysToBitmask(daysString) {
    const days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thurs', 'Fri', 'Sat'];
    const selectedDays = daysString.split(', ').map(day => day.trim());
    let bitmask = 0;

    selectedDays.forEach(day => {
        const index = days.indexOf(day);
        if (index !== -1) {
            bitmask |= (1 << (6 - index)); // Adjust bit position
        }
    });

    return bitmask.toString(2).padStart(7, '0'); // Convert to binary string
}


function updateBitmask() {
    let bitmask = 0;
    
    document.querySelectorAll('input[name="days[]"]:checked').forEach(checkbox => {
        bitmask |= (1 << (6 - checkbox.value)); // Adjust for correct bit position
    });

    let binaryString = bitmask.toString(2).padStart(7, '0');

    document.getElementById('siteOpDays').value = binaryString;
}

function aupdateBitmask() {
    let bitmask = 0;
    
    document.querySelectorAll('input[name="adays[]"]:checked').forEach(checkbox => {
        bitmask |= (1 << (6 - checkbox.value)); // Adjust for correct bit position
    });

    let binaryString = bitmask.toString(2).padStart(7, '0');

    document.getElementById('asiteOpDays').value = binaryString;
}

function showModal() {
    var modal = new bootstrap.Modal(document.getElementById('touristSitesModal'));
    modal.show();
}

function editModal(siteid) {
    var detailsModalElement = document.getElementById('showDetailsModal');
    var detailsModalInstance = bootstrap.Modal.getInstance(detailsModalElement);
    if (detailsModalInstance) {
        detailsModalInstance.hide();
    }
    const modalSiteId = document.getElementById("siteid");
    modalSiteId.value = siteid;
    var modal = new bootstrap.Modal(document.getElementById('editTouristSitesModal'));
    modal.show();
}

function detailsModal(siteid, sitename, siteimage, sitedesc, siteopdays, siteprice) {
    const displaySiteName = document.getElementById("displaySiteName");
    const displaySiteImage = document.getElementById("displayFileName");
    const displaySitePrice = document.getElementById("displaySitePrice");
    const displaySiteOpDays = document.getElementById("displaySiteSchedule");
    const displaySiteDesc = document.getElementById("displaySiteDescription");
    const displayImage = document.getElementById("displayImage");
    const toggleEditBtn = document.getElementById("showeditmodal");

    displaySiteName.value = sitename;
    displaySiteImage.value = siteimage;
    displaySitePrice.value = siteprice;
    displaySiteOpDays.value = bitmaskToDays(siteopdays);
    displaySiteDesc.value = sitedesc;
    displayImage.src = '/T-VIBES/public/uploads/' + siteimage;

    var modal = new bootstrap.Modal(document.getElementById('showDetailsModal'));
    modal.show();

    toggleEditBtn.addEventListener("click", function () {
        editModal(siteid);
    });
}

document.addEventListener("DOMContentLoaded", function () {
    const siteItems = document.querySelectorAll(".siteitem");
    const inputSiteName = document.getElementById("siteName");
    const inputSiteImage = document.getElementById("imageUpload");
    const inputSitePrice = document.getElementById("sitePrice");
    const inputaSiteOpDays = document.getElementById("asiteOpDays");
    const inputSiteDesc = document.getElementById("siteDescription");
    
    siteItems.forEach((item) => {
        item.addEventListener("click", function () {
            const sitename = this.getAttribute("data-sitename");
            const siteimage = this.getAttribute("data-siteimage");
            const sitedesc = this.getAttribute("data-sitedesc");
            const siteopdays = this.getAttribute("data-siteopdays");
            const siteprice = this.getAttribute("data-price");
            const siteid = this.getAttribute("data-siteid");
            detailsModal(siteid, sitename, siteimage, sitedesc, siteopdays, siteprice);
        });
    });

    document
        .getElementById("editSiteForm")
        .addEventListener("submit", function (event) {
            event.preventDefault();

            const formData = new FormData(this);

            fetch("../../controllers/sitecontroller.php", {
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
                    console.error("Error:", error);
                    Swal.fire({
                        icon: "error",
                        html: '<p style="font-size: 24px; font-weight: bold;">Something went wrong. Please try again later.</p>',
                        showConfirmButton: false,
                        timer: 3000,
                    });
                });
        });

        document
        .getElementById("addSiteForm")
        .addEventListener("submit", function (event) {
            event.preventDefault();

            const formData = new FormData(this);
            if (!inputSiteName.value || !inputSiteImage.files.length || !inputSitePrice.value || !inputaSiteOpDays.value || !inputSiteDesc.value) {
                Swal.fire({
                    iconHtml: '<i class="fas fa-exclamation-triangle"></i>',
                    title: "Please fill out all fields!",
                    customClass: {
                        title: "swal2-title-custom",
                        icon: "swal2-icon-custom",
                        popup: "swal-custom-popup"
                    }
                });
            }
            else {
            fetch("../../controllers/sitecontroller.php", {
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
                    console.error("Error:", error);
                    Swal.fire({
                        icon: "error",
                        html: '<p style="font-size: 24px; font-weight: bold;">Something went wrong. Please try again later.</p>',
                        showConfirmButton: false,
                        timer: 3000,
                    });
                });
            }
        });


    // document.querySelector(".modal-footer .btn-custom").addEventListener("click", function () {
    //     if (!inputSiteName.value || !inputSiteImage.value || !inputSitePrice.value || !inputSiteOpDays.value || !inputSiteDesc.value) {
    //         Swal.fire({
    //             iconHtml: '<i class="fas fa-exclamation-triangle"></i>',
    //             title: "Please fill out all fields!",
    //             customClass: {
    //                 title: "swal2-title-custom",
    //                 icon: "swal2-icon-custom",
    //                 popup: "swal-custom-popup"
    //             }
    //         });
    //     }
    //     else {
    //         Swal.fire({
    //             iconHtml: '<i class="fas fa-thumbs-up"></i>',
    //             title: "Confirm Details?",
    //             showCancelButton: true,
    //             confirmButtonText: "Yes",
    //             cancelButtonText: "No",
    //             customClass: {
    //                 title: "swal2-title-custom",
    //                 icon: "swal2-icon-custom",
    //                 popup: "swal-custom-popup",
    //                 confirmButton: "swal-custom-btn",
    //                 cancelButton: "swal-custom-btn"
    //             }
    //         }).then((result) => {
    //             if (result.isConfirmed) {
    //                 Swal.fire({
    //                     iconHtml: '<i class="fas fa-circle-check"></i>',
    //                     title: "Tourist Site Successfully Added!",
    //                     timer: 3000,
    //                     showConfirmButton: false,
    //                     customClass: {
    //                         title: "swal2-title-custom",
    //                         icon: "swal2-icon-custom",
    //                         popup: "swal-custom-popup"
    //                     }
    //                 });
    //             }
    //         });
    //     }
    // });

    // document.querySelector(".edit-modal-footer .btn-custom").addEventListener("click", function () {
    //     Swal.fire({
    //         iconHtml: '<i class="fas fa-thumbs-up"></i>',
    //         title: "Confirm Changes?",
    //         showCancelButton: true,
    //         confirmButtonText: "Yes",
    //         cancelButtonText: "No",
    //         customClass: {
    //             title: "swal2-title-custom",
    //             icon: "swal2-icon-custom",
    //             popup: "swal-custom-popup",
    //             confirmButton: "swal-custom-btn",
    //             cancelButton: "swal-custom-btn"
    //         }
    //     }).then((result) => {
    //         if (result.isConfirmed) {
    //             Swal.fire({
    //                 iconHtml: '<i class="fas fa-circle-check"></i>',
    //                 title: "Tourist Site Successfully Edited!",
    //                 timer: 3000,
    //                 showConfirmButton: false,
    //                 customClass: {
    //                     title: "swal2-title-custom",
    //                     icon: "swal2-icon-custom",
    //                     popup: "swal-custom-popup"
    //                 }
    //             });
    //         }
    //     });
    // });

    const fileInput = document.getElementById('imageUpload');
    const editfileInput = document.getElementById('editimageUpload');

    fileInput.addEventListener('change', function(event) {
        const file = event.target.files[0];
        const previewImage = document.getElementById('previewImage');
        const previewIcon = document.getElementById('previewIcon');

        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImage.src = e.target.result;
                previewImage.style.display = 'block';
                previewIcon.style.display = 'none';
            };
            reader.readAsDataURL(file);
        } else {
            previewImage.style.display = 'none';
            previewIcon.style.display = 'block';
        }
    });

    editfileInput.addEventListener('change', function(event) {
        const editfile = event.target.files[0];
        const editpreviewImage = document.getElementById('editpreviewImage');
        const editpreviewIcon = document.getElementById('editpreviewIcon');

        if (editfile) {
            const reader = new FileReader();
            reader.onload = function(e) {
                editpreviewImage.src = e.target.result;
                editpreviewImage.style.display = 'block';
                editpreviewIcon.style.display = 'none';
            };
            reader.readAsDataURL(editfile);
        } else {
            editpreviewImage.style.display = 'none';
            editpreviewIcon.style.display = 'block';
        }
    });


});
