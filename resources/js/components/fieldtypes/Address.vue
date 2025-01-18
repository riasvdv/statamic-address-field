<!--suppress ALL -->
<template>
    <fieldset class="flex flex-col gap-2 border-grey-40 py-2 rounded m-0 relative">
      <button @click="toggleMap" style="color: #576575; position: absolute; top: -35px; right: 3px;">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" height="24" width="24"><g transform="matrix(2,0,0,2,0,0)"><path d="M23.25,9V4.65a1.5,1.5,0,0,0-.943-1.393l-6-2.4a1.5,1.5,0,0,0-1.114,0L8.807,3.412a1.5,1.5,0,0,1-1.114,0L1.779,1.046a.75.75,0,0,0-1.029.7V16.119a1.5,1.5,0,0,0,.943,1.393l6,2.4a1.5,1.5,0,0,0,1.114,0l2.881-1.153" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"></path><path d="M8.25 3.519L8.25 20.019" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"></path><path d="M15.75 0.75L15.75 8.25" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"></path><path d="M18.75,15.449a.375.375,0,0,1,.375.375" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"></path><path d="M18.375,15.824a.375.375,0,0,1,.375-.375" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"></path><path d="M18.75,16.2a.375.375,0,0,1-.375-.375" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"></path><path d="M19.125,15.824a.375.375,0,0,1-.375.375" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"></path><path d="M18.75,11.324a4.5,4.5,0,0,1,4.5,4.5c0,1.921-2.688,5.576-3.909,7.138a.75.75,0,0,1-1.182,0c-1.221-1.561-3.909-5.217-3.909-7.138A4.5,4.5,0,0,1,18.75,11.324Z" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"></path></g></svg>
      </button>
      <l-map ref="map" :style="(showMap ? 'height: 450px;' : 'height: 0px;') + 'transition: all 250ms;'" :zoom="map.zoom" :center="map.center">
        <l-tile-layer :url="map.url" :attribution="map.attribution"></l-tile-layer>
        <l-marker :lat-lng.sync="map.markerLatLng" :icon="map.markerIcon" :draggable="true"></l-marker>
      </l-map>
      <div class="w-full field-inner" v-if="config.enabledFields.includes('name')">
        <label for="field_name" class="publish-field-label" v-text="__('Name')"></label>
        <text-input @blur="geoCode" v-model="value.name" id="field_name" />
      </div>
      <div class="w-full field-inner" v-if="config.enabledFields.includes('street')">
        <label for="field_street" class="publish-field-label" v-text="__('Street')"></label>
        <text-input @blur="geoCode" v-model="value.street" id="field_street" />
      </div>
      <div class="w-full field-inner" v-if="config.enabledFields.includes('street2')">
        <label for="field_street2" class="publish-field-label" v-text="__('Street 2')"></label>
        <text-input @blur="geoCode" v-model="value.street2" id="field_street2" />
      </div>
      <div class="flex gap-3">
        <div class="w-full field-inner" v-if="config.enabledFields.includes('postCode')">
          <label for="field_postCode" class="publish-field-label" v-text="__('Postcode')"></label>
          <text-input @blur="geoCode" v-model="value.postCode" id="field_postCode" />
        </div>
        <div class="w-full field-inner" v-if="config.enabledFields.includes('city')">
          <label for="field_city" class="publish-field-label" v-text="__('City')"></label>
          <text-input @blur="geoCode" v-model="value.city" id="field_city" />
        </div>
      </div>
      <div class="flex gap-3">
        <div class="w-full field-inner" v-if="config.enabledFields.includes('state')">
          <label for="field_state" class="publish-field-label" v-text="__('State')"></label>
          <text-input @blur="geoCode" v-model="value.state" id="field_state" />
        </div>
        <div class="w-full field-inner" v-if="config.enabledFields.includes('country')">
          <label for="field_country" class="publish-field-label" v-text="__('Country')"></label>
          <select-input @blur="geoCode" v-model="value.country" :options="meta.countries" id="field_country" />
        </div>
      </div>
      <div v-show="config.showCoordinates">
        <div class="flex gap-3">
          <div class="w-full field-inner">
            <label for="field_latitude" class="publish-field-label" v-text="__('Latitude')"></label>
            <text-input v-model="value.latitude" id="field_latitude" />
          </div>
          <div class="w-full field-inner">
            <label for="field_longitude" class="publish-field-label" v-text="__('Longitude')"></label>
            <text-input v-model="value.longitude" id="field_longitude" />
          </div>
        </div>
      </div>
    </fieldset>
</template>

<script>
import { Icon } from "leaflet";

const markerIcon = new Icon.Default({
  imagePath: '/vendor/statamic-address-field/images/',
  iconUrl: 'marker-icon.png',
  iconRetinaUrl: 'marker-icon-2x.png',
  shadowUrl: 'marker-shadow.png',
});

export default {
  mixins: [Fieldtype],

  data() {
      return {
          showMap: false,
          map: {
            url: 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
            attribution:
                '&copy; <a target="_blank" href="http://osm.org/copyright">OpenStreetMap</a> contributors',
            zoom: 15,
            center: [51.505, -0.159],
            markerLatLng: [51.504, -0.159],
            markerIcon: markerIcon,
          },
      };
  },

  created() {
    if (! this.value) {
      this.value = {};
      this.config.enabledFields.forEach(field => this.value[field] = '');
    }

    if (! this.value.country) {
      this.value.country = this.config.defaultCountry;
    }

    if (this.value.latitude && this.value.longitude) {
      this.map.markerLatLng = [this.value.latitude, this.value.longitude];
      this.map.center = [this.value.latitude, this.value.longitude];
    }
  },

  watch: {
      'value': {
        deep: true,
        handler: function (newValue) {
          this.update(newValue);
        }
      },

      'map.markerLatLng': function (newValue) {
        if (newValue.lat && newValue.lng) {
          this.value.latitude = newValue.lat;
          this.value.longitude = newValue.lng;
        }
      }
  },

  methods: {
    toggleMap: function () {
      this.showMap = !this.showMap;
      setTimeout(() => {
        this.$refs.map.mapObject.invalidateSize();
      }, 250);
    },
    geoCode: function() {
      if (! this.config.geoCode) {
        return;
      }

      if (! this.value.street || ! this.value.country) {
        return;
      }

      let url = `https://nominatim.openstreetmap.org/search.php?q=${this.value.street}`;

      if (this.value.street2) url+= `+${this.value.street2}`;
      if (this.value.postCode) url+= `%2C${this.value.postCode}`;
      if (this.value.city) url+= `${this.value.postCode ? '+' : '%2C'}${this.value.city}`;
      if (this.value.state) url+= `${this.value.postCode || this.value.city ? '+' : '%2C'}${this.value.state}`;
      if (this.value.country) url+= `%2C${this.value.country}`;

      const self = this;
      this.$axios.get(`${url}&format=jsonv2`)
          .then((response) => {
            if (response.status !== 200) {
              return;
            }

            if (! response.data.length > 0) {
              return;
            }

            const result = response.data[0];

            self.value.latitude = result.lat;
            self.value.longitude = result.lon;
            self.map.markerLatLng = [result.lat, result.lon];
            self.map.center = [result.lat, result.lon];
          });
    },
  }
};
</script>
