document.addEventListener("DOMContentLoaded", function () {
    document.querySelector(".add-site-box").addEventListener("click", function () {
        var addModal = new bootstrap.Modal(document.getElementById("addTouristSitesModal"));
        addModal.show();
    });

    document.getElementById("imageUpload").addEventListener("change", function (event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                document.getElementById("previewImage").src = e.target.result;
                document.getElementById("previewImage").style.display = "block";
                document.getElementById("previewIcon").style.display = "none";
            };
            reader.readAsDataURL(file);
        }
    });

    document.getElementById("editImageUpload").addEventListener("change", function (event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                document.getElementById("editPreviewImage").src = e.target.result;
                document.getElementById("editPreviewImage").style.display = "block";
                document.getElementById("editPreviewIcon").style.display = "none";
            };
            reader.readAsDataURL(file);
        }
    });

    document.getElementById("submitAddSite").addEventListener("click", function (event) {
        event.preventDefault();

        let siteName = document.getElementById("siteName").value.trim();
        let sitePrice = document.getElementById("sitePrice").value.trim();
        let siteDescription = document.getElementById("siteDescription").value.trim();
        let imageUpload = document.getElementById("imageUpload").files.length;
        let selectedDays = document.querySelectorAll("input[name='adays[]']:checked").length;

        if (!siteName || !sitePrice || !siteDescription || imageUpload === 0 || selectedDays === 0) {
            Swal.fire({
                iconHtml: '<i class="fas fa-exclamation-circle"></i>',
                title: "Please fill out all fields!",
                timer: 3000,
                showConfirmButton: false,
                customClass: {
                    title: "swal2-title-custom",
                    icon: "swal2-icon-custom",
                    popup: "swal-custom-popup"
                }
            });
            return;
        }

        Swal.fire({
            title: "Confirm Details?",
            iconHtml: '<i class="fas fa-thumbs-up"></i>',
            showCancelButton: true,
            confirmButtonText: "Yes",
            cancelButtonText: "No",
            customClass: {
                title: "swal2-title-custom",
                icon: "swal2-icon-custom",
                popup: "swal-custom-popup",
                confirmButton: "swal-custom-btn",
                cancelButton: "swal-custom-btn"
            }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: "Tourist Site Added Successfully!",
                    iconHtml: '<i class="fas fa-circle-check"></i>',
                    timer: 3000,
                    showConfirmButton: false,
                    customClass: {
                        title: "swal2-title-custom",
                        icon: "swal2-icon-custom",
                        popup: "swal-custom-popup"
                    }
                }).then(() => {
                    document.getElementById("addSiteForm").submit();
                });
            }
        });
    });

    document.querySelectorAll(".info-box").forEach((box) => {
        box.addEventListener("click", function () {
            var siteId = this.getAttribute("data-siteid");
            var siteName = this.getAttribute("data-sitename");
            var siteImage = this.getAttribute("data-siteimage");
            var siteDescription = this.getAttribute("data-sitedescription");
            var opdays = this.getAttribute("data-opdays");
            var price = this.getAttribute("data-price");

            document.getElementById("editSiteId").value = siteId;
            document.getElementById("editSiteName").value = siteName;
            document.getElementById("editSitePrice").value = price;
            document.getElementById("editSiteDescription").value = siteDescription;

            var days = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];
            for (var i = 0; i < 7; i++) {
                document.getElementById("edit" + days[i]).checked = opdays[i] === "1";
            }

            if (siteImage) {
                document.getElementById("editPreviewImage").src = "/T-VIBES/public/uploads/" + siteImage;
                document.getElementById("editPreviewImage").style.display = "block";
                document.getElementById("editPreviewIcon").style.display = "none";
            } else {
                document.getElementById("editPreviewImage").style.display = "none";
                document.getElementById("editPreviewIcon").style.display = "block";
            }

            var editModal = new bootstrap.Modal(document.getElementById("editTouristSitesModal"));
            editModal.show();
        });
    });

    document.querySelector("#editTouristSitesModal .btn-custom").addEventListener("click", function (event) {
        event.preventDefault();

        let originalSiteName = document.getElementById("editSiteName").getAttribute("data-original");
        let originalSitePrice = document.getElementById("editSitePrice").getAttribute("data-original");
        let originalSiteDescription = document.getElementById("editSiteDescription").getAttribute("data-original");
        let originalDays = document.getElementById("editSiteId").getAttribute("data-original-days");

        let siteName = document.getElementById("editSiteName").value.trim();
        let sitePrice = document.getElementById("editSitePrice").value.trim();
        let siteDescription = document.getElementById("editSiteDescription").value.trim();
        let selectedDays = Array.from(document.querySelectorAll("input[name='editDays[]']:checked"))
            .map(day => day.value).join("");

        if (!siteName || !sitePrice || !siteDescription || selectedDays.length === 0) {
            Swal.fire({
                iconHtml: '<i class="fas fa-exclamation-circle"></i>',
                title: "Please fill out all fields!",
                timer: 3000,
                showConfirmButton: false,
                customClass: {
                    title: "swal2-title-custom",
                    icon: "swal2-icon-custom",
                    popup: "swal-custom-popup"
                }
            });
            return;
        }

        if (siteName === originalSiteName && sitePrice === originalSitePrice && siteDescription === originalSiteDescription && selectedDays === originalDays) {
            Swal.fire({
                iconHtml: '<i class="fas fa-info-circle"></i>',
                title: "No changes were made!",
                timer: 3000,
                showConfirmButton: false,
                customClass: {
                    title: "swal2-title-custom",
                    icon: "swal2-icon-custom",
                    popup: "swal-custom-popup"
                }
            });
            return;
        }

        Swal.fire({
            title: "Confirm Changes?",
            iconHtml: '<i class="fas fa-thumbs-up"></i>',
            showCancelButton: true,
            confirmButtonText: "Yes",
            cancelButtonText: "No",
            customClass: {
                title: "swal2-title-custom",
                icon: "swal2-icon-custom",
                popup: "swal-custom-popup",
                confirmButton: "swal-custom-btn",
                cancelButton: "swal-custom-btn"
            }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: "Tourist Site Updated Successfully!",
                    iconHtml: '<i class="fas fa-circle-check"></i>',
                    timer: 2000,
                    showConfirmButton: false,
                    customClass: {
                        title: "swal2-title-custom",
                        icon: "swal2-icon-custom",
                        popup: "swal-custom-popup"
                    }
                }).then(() => {
                    document.querySelector("#editSiteForm").submit();
                });
            }
        });
    });
});