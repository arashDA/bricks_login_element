document.addEventListener("DOMContentLoaded", function() {

    const providerSelect = document.getElementById("otp_sms_provider");
    const providerFields = document.querySelectorAll(".provider-fields");

    function toggleProviders() {
        providerFields.forEach(field => {
            field.style.display = field.dataset.provider === providerSelect.value ? "block" : "none";
        });
    }

    providerSelect.addEventListener("change", toggleProviders);
    toggleProviders();
});
