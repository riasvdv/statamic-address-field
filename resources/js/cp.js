import Address from "./Address.vue";

Statamic.booting(() => {
  Statamic.$components.register("address-fieldtype", Address);
});
