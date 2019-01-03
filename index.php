<?php
  //TODO: Move Query to Function / Class so it's can useable
  
  require_once __DIR__ . '/vendor/autoload.php';
  include "lib/utility.php";

  $klein = new \Klein\Klein();

  header('Content-type:application/json');
  header("Access-Control-Allow-Origin: *");

  $klein->with('/region', function () use ($klein) {
    $klein->respond('GET', '/?', function ($request, $response) {
      $sql = "SELECT kabupaten_ from tb_kabupaten WHERE provinsi = 'Jawa Timur' ORDER BY kabupaten_";
      $result = msq_fetch_all($sql);

      $data['kota_kab'] = array();
      $index = 0;
      foreach($result as $item){
        $reg = array();
        $reg['id'] = $index++;
        $reg['name'] = $item['kabupaten_'];
        array_push($data['kota_kab'], $reg);
      }


      return json_encode($data);
    });
  });

  $klein->with('/place', function () use ($klein) {
    $klein->respond('GET', '/?', function ($request, $response) {
      $sql = "SELECT jenis_fitur, id from tb_markers GROUP BY jenis_fitur";
      $result['places'] = msq_fetch_all($sql);

      return json_encode($result);
    });
  });

  $klein->with('/polygon', function () use ($klein) {

      $klein->respond('GET', '/?', function ($request, $response) {
        $json = array();

        $sql = "SELECT id, kabupaten_ AS kabupaten, kode, ibukota, provinsi, bupati_wal, wakil, batas_utar, batas_sela, batas_bara, batas_timu, lmb_geo FROM tb_kabupaten WHERE kabupaten_ = 'jember'";
        $result = msq_fetch($sql);

        $sql = "SELECT AsText(SHAPE) AS geometry FROM tb_kabupaten WHERE kabupaten_='jember'";
        $raw = msq_fetch($sql);
        $polygon = geoPHP::load($raw['geometry'], 'wkt');
        $data = $polygon->asArray();

        $json['type'] = "Feature";
        $json['id'] = "1";
        $json['properties'] = $result;
        $json['geometry']['type'] = $polygon->geometryType();
        $json['geometry']['coordinates'] = $data;

        return json_encode($json);

      });

      $klein->respond('GET', '/[:name]', function ($request, $response) {
        $json = array();

        $region = ms_escape($request->name);
        $sql = "SELECT kabupaten_ AS Kabupaten, kode Kode, ibukota 'Ibu Kota', provinsi Provinsi, bupati_wal 'Bupati/Wali Kota', wakil Wakil, batas_utar 'Batas Utara', batas_sela 'Batas Selatan', batas_bara 'Batas Barat', batas_timu 'Batas Timur', lmb_geo FROM tb_kabupaten WHERE kabupaten_ = '$region'";
        $result = msq_fetch($sql);

        if($result == null){
          $json['code'] = 404;
          $json['message'] = "Data Not Found!";
        }else{
          $sql = "SELECT AsText(SHAPE) AS geometry FROM tb_kabupaten WHERE kabupaten_='$region'";
          $raw = msq_fetch($sql);
          $polygon = geoPHP::load($raw['geometry'], 'wkt');
          $data = $polygon->asArray();

          $json['type'] = "Feature";
          $json['id'] = "1";
          $json['properties'] = $result;
          $json['geometry']['type'] = $polygon->geometryType();
          $json['geometry']['coordinates'] = $data;
        }
        return json_encode($json);
      });

  });

  $klein->with('/markers', function () use ($klein) {

    $klein->respond('GET', '/?', function ($request, $response) {
      $data = array();

      $sql = "SELECT lat_long FROM tb_markers";
      $geometries = msq_fetch_all($sql);

      $sql = "SELECT jenis_fitur, nama_fitur, kategori, kota_kab, deskripsi FROM tb_markers";
      $properties = msq_fetch_all($sql);

      if($geometries == null){
        $data['code'] = 400;
        $data['message'] = "Data Not Found!";

        return json_encode($data);
      }

      $data['type'] = 'FeatureCollection';
      $features = array();

      $index = 0;
      foreach($geometries as $geo){
        $pointstr = explode(",", $geo['lat_long']);
        $geometry['type'] = "Point";
        $geometry['coordinates'] = array(floatval($pointstr[1]), floatval($pointstr[0]));

        $propertie = $properties[$index];
        $features[$index]['type'] = "Feature";
        $features[$index]['geometry'] = $geometry;
        $features[$index]['properties'] = $propertie;

        $index++;
      }
      $data['features'] = $features;
      return json_encode($data);
    });

    $klein->respond('GET', '/[:jenis]/?', function ($request, $response) {
      $data = array();

      $jenis = ms_escape($request->jenis);
      $jenis = str_replace("_", " ", $jenis);

      $sql = "SELECT lat_long FROM tb_markers WHERE jenis_fitur = '$jenis'";
      $geometries = msq_fetch_all($sql);

      $sql = "SELECT jenis_fitur 'Jenis Instansi', nama_fitur 'Nama Instansi', kategori 'Kategori', kota_kab 'Kab/Kota', deskripsi 'Deskripsi', lat_long 'Koordinat' FROM tb_markers WHERE jenis_fitur = '$jenis'";
      $properties = msq_fetch_all($sql);

      if($geometries == null){
        $data['code'] = 400;
        $data['message'] = "Data Not Found!";

        return json_encode($data);
      }

      $data['type'] = 'FeatureCollection';
      $features = array();

      $index = 0;
      foreach($geometries as $geo){
        $pointstr = explode(",", $geo['lat_long']);
        $geometry['type'] = "Point";
        $geometry['coordinates'] = array(floatval($pointstr[1]), floatval($pointstr[0]));

        $propertie = $properties[$index];
        $features[$index]['type'] = "Feature";
        $features[$index]['geometry'] = $geometry;
        $features[$index]['properties'] = $propertie;

        $index++;
      }
      $data['features'] = $features;
      return json_encode($data);
    });

    $klein->respond('GET', '/[:jenis]/[:region]/?', function ($request, $response) {
      $data = array();

      $jenis = ms_escape($request->jenis);
      $region = ms_escape($request->region);

      $jenis = str_replace("_", " ", $jenis);

      $sql = "SELECT lat_long FROM tb_markers WHERE jenis_fitur = '$jenis' AND kota_kab = '$region'";
      $geometries = msq_fetch_all($sql);

      $sql = "SELECT jenis_fitur, nama_fitur, kategori, kota_kab, deskripsi FROM tb_markers WHERE jenis_fitur = '$jenis' AND kota_kab = '$region'";
      $properties = msq_fetch_all($sql);

      if($geometries == null){
        $data['code'] = 400;
        $data['message'] = "Data Not Found!";

        return json_encode($data);
      }

      $data['type'] = 'FeatureCollection';
      $features = array();

      $index = 0;
      foreach($geometries as $geo){
        $pointstr = explode(",", $geo['lat_long']);
        $geometry['type'] = "Point";
        $geometry['coordinates'] = array(floatval($pointstr[1]), floatval($pointstr[0]));

        $propertie = $properties[$index];
        $features[$index]['type'] = "Feature";
        $features[$index]['geometry'] = $geometry;
        $features[$index]['properties'] = $propertie;

        $index++;
      }
      $data['features'] = $features;
      return json_encode($data);
    });

  });

  $klein->respond('GET', '/?', function ($request) {
    $json = array();
    $json['code'] = 200;
    $json['message'] = "Welcome to GIS API!";

    return json_encode($json);
  });

  $klein->respond('404', function ($request) {
    $json = array();
    $json['code'] = 404;
    $json['message'] = "Page Not Found!";

    return json_encode($json);
  });


//marker/{jenis}/{kategori}/?region=
$klein->dispatch();
?>
