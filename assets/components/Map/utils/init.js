function init(google) {
  class Rectangle extends google.maps.Rectangle {
    constructor(options) {
      super(options);
    }

    getPos() {
      const sw = this.getBounds().getSouthWest();
      const ne = this.getBounds().getNorthEast();
      const scale = Math.pow(2, this.map.getZoom());
      const proj = this.map.getProjection();
      const bounds = this.map.getBounds();
      const nw = proj.fromLatLngToPoint(
        new google.maps.LatLng(
          bounds.getNorthEast().lat(),
          bounds.getSouthWest().lng()
        )
      );

      const point = proj.fromLatLngToPoint(sw);
      const point1 = proj.fromLatLngToPoint(ne);

      const nePoint = new google.maps.Point(
        Math.floor((point1.x - nw.x) * scale),
        Math.floor((point1.y - nw.y) * scale)
      );
      const swPoint = new google.maps.Point(
        Math.floor((point.x - nw.x) * scale),
        Math.floor((point.y - nw.y) * scale)
      );

      return {
        ne: nePoint,
        sw: swPoint,
      };
    }

    getLatLng() {
      const ne = this.getBounds().getNorthEast();
      const sw = this.getBounds().getSouthWest();

      const nw = new google.maps.LatLng(ne.lat(), sw.lng());
      const se = new google.maps.LatLng(sw.lat(), ne.lng());

      return {
        nw: nw.toJSON(),
        ne: ne.toJSON(),
        sw: sw.toJSON(),
        se: se.toJSON(),
      };
    }
  }

  class MyMap extends google.maps.Map {
    constructor(div, opt) {
      super(div, opt);
    }
  }

  return {
    initMap: (div, options) => new MyMap(div, options),
    initRect: (options) => new Rectangle(options),
  };
}

export default init;
