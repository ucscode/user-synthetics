"use strict";

$(function() {

    new class {

        template = $("#template").get(0);
        model = template.dataset.model;

        constructor() {
            this.#init();
            $("#template-adder").on("click", () => this.addItem());
        }

        #init() {
            const Json = atob(this.template.dataset.detail);
            let details = JSON.parse(Json);
            if(!Object.keys(details).length) return this.addItem();
            for(let key in details) {
                let value = details[key];
                this.addItem(key, value);
            }
        }

        addItem(key = '', value = '') {
            const fragment = template.content.cloneNode(true); // document
            const root = fragment.firstElementChild;

            let keyInput = root.querySelector("[data-key]");
            let valueInput = root.querySelector("[data-value]");

            keyInput.value = key;

            if(valueInput) {
                if(valueInput.type !== 'checkbox') valueInput.value = value;
                else {
                    valueInput.checked = parseInt(value) == 1;
                    let valueAlt = valueInput.previousElementSibling;
                    if(valueInput.checked) this.#removeElement(valueAlt);
                    const self = this;
                    valueInput.addEventListener("change", function() {
                        if(this.checked) self.#removeElement(valueAlt);
                        else valueInput.parentElement.insertBefore(valueAlt, valueInput);
                    })
                }
            }

            fragment.querySelector("[data-trash]").addEventListener("click", e => {
                this.#trashItem(
                    keyInput.value.trim(), 
                    valueInput.type == 'checkbox' ? valueInput.checked : valueInput.value.trim(),
                    root
                );
            });

            $("#template-block").append(fragment);
        }


        #removeElement(element) {
            if(element.parentElement) {
                element.parentElement.removeChild(element);
            }
        };

        #trashItem(key, value, root) {
            if($("#template-block").children().length === 1) 
                return iziToast.warning({ message: "There should be at least one detail left" });

            if(key !== '' || (value && value !== '')) {
                bootbox.confirm({
                    message: "Are you sure you want to remove the detail?",
                    callback: function(yes) {
                        if(yes) root.parentElement.removeChild(root);
                    },
                    size: 'small',
                    closeButton: false
                });
            } else {
                root.parentElement.removeChild(root);
            }
        }

    }
    
});