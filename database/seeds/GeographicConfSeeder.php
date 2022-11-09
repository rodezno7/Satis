<?php

use App\City;
use App\Country;
use App\State;
use App\Zone;
use Illuminate\Database\Seeder;

class GeographicConfSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $business_id = null;

        /** If business_id not setted, exit */
        if (is_null($business_id)) {
            return true;
        }

        /** Insert default country */
        $country = Country::updateOrCreate(
            [
                'name' => 'El Salvador',
                'short_name' => 'SV',
                'business_id' => $business_id
            ],
            [
                'code' => '503',
                'flag' => '1603645631ESA.png'
            ]
        );

        /** Insert default zones */
        $zones = [
            'CENTRAL',
            'ORIENTAL',
            'OCCIDENTAL',
            'PARACENTRAL',
            'NORTE'    
        ];

        foreach ($zones as $zone) {
            Zone::updateOrCreate(
                [
                    'name' => $zone,
                    'business_id' => $business_id
                ]
            );
        }

        /** Insert default states */
        $states = [
            ['name' => 'San Salvador', 'zip_code' => '', 'zone' => 'CENTRAL'],
            ['name' => 'Cabañas', 'zip_code' => '01201', 'zone' => 'CENTRAL'],
            ['name' => 'La Libertad', 'zip_code' => '00000', 'zone' => 'CENTRAL'],
            ['name' => 'San Vicente', 'zip_code' => '', 'zone' => 'CENTRAL'],
            ['name' => 'San Miguel', 'zip_code' => '', 'zone' => 'ORIENTAL'],
            ['name' => 'Morazán', 'zip_code' => '', 'zone' => 'ORIENTAL'],
            ['name' => 'La Unión', 'zip_code' => '', 'zone' => 'ORIENTAL'],
            ['name' => 'Usulután', 'zip_code' => '', 'zone' => 'ORIENTAL'],
            ['name' => 'Ahuachapán', 'zip_code' => '02101', 'zone' => 'OCCIDENTAL'],
            ['name' => 'Santa Ana', 'zip_code' => '', 'zone' => 'OCCIDENTAL'],
            ['name' => 'Sonsonate', 'zip_code' => '', 'zone' => 'OCCIDENTAL'],
            ['name' => 'Cuscatlán', 'zip_code' => '00000', 'zone' => 'PARACENTRAL'],
            ['name' => 'La Paz', 'zip_code' => '', 'zone' => 'PARACENTRAL'],
            ['name' => 'Chalatenango', 'zip_code' => '01301', 'zone' => 'NORTE']            
        ];

        $zones = Zone::where('business_id', $business_id)
            ->select('id', 'name')->get();

        foreach ($states as $s) {
            $zone = $zones->where('name', $s['zone'])->first();

            State::updateOrCreate(
                [
                    'name' => $s['name'],
                    'business_id' => $business_id
                ],
                [
                    'zip_code' => $s['zip_code'],
                    'country_id' => $country->id,
                    'zone_id' => $zone->id,
                ]
            );
        }

        /** Insert default cities */
        $cities = [
            ['name' => 'San Salvador', 'status' => '1', 'state' => 'San Salvador'],
            ['name' => 'Aguilares', 'status' => '1', 'state' => 'San Salvador'],
            ['name' => 'Apopa', 'status' => '1', 'state' => 'San Salvador'],
            ['name' => 'Ayutuxtepeque', 'status' => '1', 'state' => 'San Salvador'],
            ['name' => 'Cuscatancingo', 'status' => '1', 'state' => 'San Salvador'],
            ['name' => 'Delgado', 'status' => '1', 'state' => 'San Salvador'],
            ['name' => 'Guazapa', 'status' => '1', 'state' => 'San Salvador'],
            ['name' => 'Ilopango', 'status' => '1', 'state' => 'San Salvador'],
            ['name' => 'Mejicanos', 'status' => '1', 'state' => 'San Salvador'],
            ['name' => 'Nejapa', 'status' => '1', 'state' => 'San Salvador'],
            ['name' => 'Panchimalco', 'status' => '1', 'state' => 'San Salvador'],
            ['name' => 'Rosario de Mora', 'status' => '1', 'state' => 'San Salvador'],
            ['name' => 'San Marcos', 'status' => '1', 'state' => 'San Salvador'],
            ['name' => 'San Martín', 'status' => '1', 'state' => 'San Salvador'],
            ['name' => 'Soyapango', 'status' => '1', 'state' => 'San Salvador'],
            ['name' => 'Santo Tomás', 'status' => '1', 'state' => 'San Salvador'],
            ['name' => 'Tonacatepeque', 'status' => '1', 'state' => 'San Salvador'],
            ['name' => 'El Paisnal', 'status' => '1', 'state' => 'San Salvador'],
            ['name' => 'Santiago Texacuangos', 'status' => '1', 'state' => 'San Salvador'],
            ['name' => 'San Miguel', 'status' => '1', 'state' => 'San Miguel'],
            ['name' => 'Ciudad Barrios', 'status' => '1', 'state' => 'San Miguel'],
            ['name' => 'Comacarán', 'status' => '1', 'state' => 'San Miguel'],
            ['name' => 'El Tránsito', 'status' => '1', 'state' => 'San Miguel'],
            ['name' => 'Moncagua', 'status' => '1', 'state' => 'San Miguel'],
            ['name' => 'San Jorge', 'status' => '1', 'state' => 'San Miguel'],
            ['name' => 'Carolina', 'status' => '1', 'state' => 'San Miguel'],
            ['name' => 'Chapeltique', 'status' => '1', 'state' => 'San Miguel'],
            ['name' => 'Chinameca', 'status' => '1', 'state' => 'San Miguel'],
            ['name' => 'Chirilagua', 'status' => '1', 'state' => 'San Miguel'],
            ['name' => 'Lolotique', 'status' => '1', 'state' => 'San Miguel'],
            ['name' => 'Nueva Guadalupe', 'status' => '1', 'state' => 'San Miguel'],
            ['name' => 'Nuevo Edén de San Juan', 'status' => '1', 'state' => 'San Miguel'],
            ['name' => 'Quelepa', 'status' => '1', 'state' => 'San Miguel'],
            ['name' => 'San Antonio del Mosco', 'status' => '1', 'state' => 'San Miguel'],
            ['name' => 'San Gerardo', 'status' => '1', 'state' => 'San Miguel'],
            ['name' => 'San Luis de la Reina', 'status' => '1', 'state' => 'San Miguel'],
            ['name' => 'San Rafael Oriente', 'status' => '1', 'state' => 'San Miguel'],
            ['name' => 'Sesori', 'status' => '1', 'state' => 'San Miguel'],
            ['name' => 'Uluazapa', 'status' => '1', 'state' => 'San Miguel'],
            ['name' => 'Ahuachapán', 'status' => '1', 'state' => 'Ahuachapán'],
            ['name' => 'Atiquizaya', 'status' => '1', 'state' => 'Ahuachapán'],
            ['name' => 'Apaneca', 'status' => '1', 'state' => 'Ahuachapán'],
            ['name' => 'San Francisco Menéndez', 'status' => '1', 'state' => 'Ahuachapán'],
            ['name' => 'Tacuba', 'status' => '1', 'state' => 'Ahuachapán'],
            ['name' => 'Turín', 'status' => '1', 'state' => 'Ahuachapán'],
            ['name' => 'Concepción de Ataco', 'status' => '1', 'state' => 'Ahuachapán'],
            ['name' => 'El Refugio', 'status' => '1', 'state' => 'Ahuachapán'],
            ['name' => 'Guaymango', 'status' => '1', 'state' => 'Ahuachapán'],
            ['name' => 'Jujutla', 'status' => '1', 'state' => 'Ahuachapán'],
            ['name' => 'San Lorenzo', 'status' => '1', 'state' => 'Ahuachapán'],
            ['name' => 'San Pedro Puxtla', 'status' => '1', 'state' => 'Ahuachapán'],
            ['name' => 'Sensuntepeque', 'status' => '1', 'state' => 'Cabañas'],
            ['name' => 'Tejutepeque', 'status' => '1', 'state' => 'Cabañas'],
            ['name' => 'Ilobasco', 'status' => '1', 'state' => 'Cabañas'],
            ['name' => 'Cinquera', 'status' => '1', 'state' => 'Cabañas'],
            ['name' => 'Dolores / Villa Dolores', 'status' => '1', 'state' => 'Cabañas'],
            ['name' => 'Guacotecti', 'status' => '1', 'state' => 'Cabañas'],
            ['name' => 'Jutiapa', 'status' => '1', 'state' => 'Cabañas'],
            ['name' => 'San Isidro', 'status' => '1', 'state' => 'Cabañas'],
            ['name' => 'Victoria', 'status' => '1', 'state' => 'Cabañas'],
            ['name' => 'Chalatenango', 'status' => '1', 'state' => 'Chalatenango'],
            ['name' => 'La Palma', 'status' => '1', 'state' => 'Chalatenango'],
            ['name' => 'Nueva Concepción', 'status' => '1', 'state' => 'Chalatenango'],
            ['name' => 'San Fernando', 'status' => '1', 'state' => 'Chalatenango'],
            ['name' => 'Tejutla', 'status' => '1', 'state' => 'Chalatenango'],
            ['name' => 'Agua Caliente', 'status' => '1', 'state' => 'Chalatenango'],
            ['name' => 'Arcatao', 'status' => '1', 'state' => 'Chalatenango'],
            ['name' => 'Azacualpa', 'status' => '1', 'state' => 'Chalatenango'],
            ['name' => 'Citalá', 'status' => '1', 'state' => 'Chalatenango'],
            ['name' => 'Comalapa', 'status' => '1', 'state' => 'Chalatenango'],
            ['name' => 'Concepción Quezaltepeque', 'status' => '1', 'state' => 'Chalatenango'],
            ['name' => 'Dulce Nombre de María', 'status' => '1', 'state' => 'Chalatenango'],
            ['name' => 'El Carrizal', 'status' => '1', 'state' => 'Chalatenango'],
            ['name' => 'El Paraíso', 'status' => '1', 'state' => 'Chalatenango'],
            ['name' => 'La Laguna', 'status' => '1', 'state' => 'Chalatenango'],
            ['name' => 'La Reina', 'status' => '1', 'state' => 'Chalatenango'],
            ['name' => 'Las Vueltas', 'status' => '1', 'state' => 'Chalatenango'],
            ['name' => 'Nombre de Jesús', 'status' => '1', 'state' => 'Chalatenango'],
            ['name' => 'Nueva Trinidad', 'status' => '1', 'state' => 'Chalatenango'],
            ['name' => 'Ojos de Agua', 'status' => '1', 'state' => 'Chalatenango'],
            ['name' => 'Potonico', 'status' => '1', 'state' => 'Chalatenango'],
            ['name' => 'San Antonio de la Cruz', 'status' => '1', 'state' => 'Chalatenango'],
            ['name' => 'San Antonio Los Ranchos', 'status' => '1', 'state' => 'Chalatenango'],
            ['name' => 'San Francisco Lempa', 'status' => '1', 'state' => 'Chalatenango'],
            ['name' => 'San Francisco Morazán', 'status' => '1', 'state' => 'Chalatenango'],
            ['name' => 'San Ignacio', 'status' => '1', 'state' => 'Chalatenango'],
            ['name' => 'San Isidro Labrador', 'status' => '1', 'state' => 'Chalatenango'],
            ['name' => 'San José Cancasque / Cancasque', 'status' => '1', 'state' => 'Chalatenango'],
            ['name' => 'San José Las Flores / Las Flores', 'status' => '1', 'state' => 'Chalatenango'],
            ['name' => 'San Luis del Carmen', 'status' => '1', 'state' => 'Chalatenango'],
            ['name' => 'San Miguel de Mercedes', 'status' => '1', 'state' => 'Chalatenango'],
            ['name' => 'San Rafael', 'status' => '1', 'state' => 'Chalatenango'],
            ['name' => 'Santa Rita', 'status' => '1', 'state' => 'Chalatenango'],
            ['name' => 'Cojutepeque', 'status' => '1', 'state' => 'Cuscatlán'],
            ['name' => 'Suchitoto', 'status' => '1', 'state' => 'Cuscatlán'],
            ['name' => 'San José Guayabal', 'status' => '1', 'state' => 'Cuscatlán'],
            ['name' => 'San Pedro Perulapán', 'status' => '1', 'state' => 'Cuscatlán'],
            ['name' => 'Candelaria', 'status' => '1', 'state' => 'Cuscatlán'],
            ['name' => 'El Rosario', 'status' => '1', 'state' => 'Cuscatlán'],
            ['name' => 'Monte San Juan', 'status' => '1', 'state' => 'Cuscatlán'],
            ['name' => 'Oratorio de Concepción', 'status' => '1', 'state' => 'Cuscatlán'],
            ['name' => 'San Bartolomé Perulapía', 'status' => '1', 'state' => 'Cuscatlán'],
            ['name' => 'San Cristóbal', 'status' => '1', 'state' => 'Cuscatlán'],
            ['name' => 'San Rafael Cedros', 'status' => '1', 'state' => 'Cuscatlán'],
            ['name' => 'San Ramón', 'status' => '1', 'state' => 'Cuscatlán'],
            ['name' => 'Santa Cruz Analquito', 'status' => '1', 'state' => 'Cuscatlán'],
            ['name' => 'Santa Cruz Michapa', 'status' => '1', 'state' => 'Cuscatlán'],
            ['name' => 'Tenancingo', 'status' => '1', 'state' => 'Cuscatlán'],
            ['name' => 'El Carmen', 'status' => '1', 'state' => 'Cuscatlán'],
            ['name' => 'Antiguo Cuscatlán', 'status' => '1', 'state' => 'La Libertad'],
            ['name' => 'Ciudad Arce', 'status' => '1', 'state' => 'La Libertad'],
            ['name' => 'Colón', 'status' => '1', 'state' => 'La Libertad'],
            ['name' => 'La Libertad', 'status' => '1', 'state' => 'La Libertad'],
            ['name' => 'Santa Tecla', 'status' => '1', 'state' => 'La Libertad'],
            ['name' => 'San Juan Opico', 'status' => '1', 'state' => 'La Libertad'],
            ['name' => 'Quezaltepeque', 'status' => '1', 'state' => 'La Libertad'],
            ['name' => 'Comasagua', 'status' => '1', 'state' => 'La Libertad'],
            ['name' => 'Chiltiupán', 'status' => '1', 'state' => 'La Libertad'],
            ['name' => 'Huizúcar', 'status' => '1', 'state' => 'La Libertad'],
            ['name' => 'Jayaque', 'status' => '1', 'state' => 'La Libertad'],
            ['name' => 'Jicalapa', 'status' => '1', 'state' => 'La Libertad'],
            ['name' => 'Nuevo Cuscatlán', 'status' => '1', 'state' => 'La Libertad'],
            ['name' => 'Sacacoyo', 'status' => '1', 'state' => 'La Libertad'],
            ['name' => 'San José Villanueva', 'status' => '1', 'state' => 'La Libertad'],
            ['name' => 'San Matías', 'status' => '1', 'state' => 'La Libertad'],
            ['name' => 'San Pablo Tacachico', 'status' => '1', 'state' => 'La Libertad'],
            ['name' => 'Talnique', 'status' => '1', 'state' => 'La Libertad'],
            ['name' => 'Tamanique', 'status' => '1', 'state' => 'La Libertad'],
            ['name' => 'Teotepeque', 'status' => '1', 'state' => 'La Libertad'],
            ['name' => 'Tepecoyo', 'status' => '1', 'state' => 'La Libertad'],
            ['name' => 'Zaragoza', 'status' => '1', 'state' => 'La Libertad'],
            ['name' => 'Corinto', 'status' => '1', 'state' => 'Morazán'],
            ['name' => 'Osicala', 'status' => '1', 'state' => 'Morazán'],
            ['name' => 'Perquín', 'status' => '1', 'state' => 'Morazán'],
            ['name' => 'Arambala', 'status' => '1', 'state' => 'Morazán'],
            ['name' => 'Cacaopera', 'status' => '1', 'state' => 'Morazán'],
            ['name' => 'Chilanga', 'status' => '1', 'state' => 'Morazán'],
            ['name' => 'Delicias de Concepción', 'status' => '1', 'state' => 'Morazán'],
            ['name' => 'El Divisadero', 'status' => '1', 'state' => 'Morazán'],
            ['name' => 'El Rosario', 'status' => '1', 'state' => 'Morazán'],
            ['name' => 'Gualococti', 'status' => '1', 'state' => 'Morazán'],
            ['name' => 'Guatajiagua', 'status' => '1', 'state' => 'Morazán'],
            ['name' => 'Joateca', 'status' => '1', 'state' => 'Morazán'],
            ['name' => 'Jocoaitique', 'status' => '1', 'state' => 'Morazán'],
            ['name' => 'Jocoro', 'status' => '1', 'state' => 'Morazán'],
            ['name' => 'Lolotiquillo', 'status' => '1', 'state' => 'Morazán'],
            ['name' => 'Meanguera', 'status' => '1', 'state' => 'Morazán'],
            ['name' => 'San Carlos', 'status' => '1', 'state' => 'Morazán'],
            ['name' => 'San Fernando', 'status' => '1', 'state' => 'Morazán'],
            ['name' => 'San Francisco Gotera', 'status' => '1', 'state' => 'Morazán'],
            ['name' => 'San Isidro', 'status' => '1', 'state' => 'Morazán'],
            ['name' => 'San Simón', 'status' => '1', 'state' => 'Morazán'],
            ['name' => 'Sensembra', 'status' => '1', 'state' => 'Morazán'],
            ['name' => 'Sociedad', 'status' => '1', 'state' => 'Morazán'],
            ['name' => 'Torola', 'status' => '1', 'state' => 'Morazán'],
            ['name' => 'Yamabal', 'status' => '1', 'state' => 'Morazán'],
            ['name' => 'Yoloaiquín', 'status' => '1', 'state' => 'Morazán'],
            ['name' => 'El Rosario', 'status' => '1', 'state' => 'La Paz'],
            ['name' => 'Olocuilta', 'status' => '1', 'state' => 'La Paz'],
            ['name' => 'San Francisco Chinameca', 'status' => '1', 'state' => 'La Paz'],
            ['name' => 'San Juan Nonualco', 'status' => '1', 'state' => 'La Paz'],
            ['name' => 'San Luis Talpa', 'status' => '1', 'state' => 'La Paz'],
            ['name' => 'Santiago Nonualco', 'status' => '1', 'state' => 'La Paz'],
            ['name' => 'San Pedro Nonualco', 'status' => '1', 'state' => 'La Paz'],
            ['name' => 'San Rafael Obrajuelo', 'status' => '1', 'state' => 'La Paz'],
            ['name' => 'San Miguel Tepezontes', 'status' => '1', 'state' => 'La Paz'],
            ['name' => 'Cuyultitán', 'status' => '1', 'state' => 'La Paz'],
            ['name' => 'Jerusalén', 'status' => '1', 'state' => 'La Paz'],
            ['name' => 'Mercedes La Ceiba', 'status' => '1', 'state' => 'La Paz'],
            ['name' => 'Paraíso de Osorio', 'status' => '1', 'state' => 'La Paz'],
            ['name' => 'San Antonio Masahuat', 'status' => '1', 'state' => 'La Paz'],
            ['name' => 'San Emigdio', 'status' => '1', 'state' => 'La Paz'],
            ['name' => 'San Juan Talpa', 'status' => '1', 'state' => 'La Paz'],
            ['name' => 'San Juan Tepezontes', 'status' => '1', 'state' => 'La Paz'],
            ['name' => 'San Luis La Herradura', 'status' => '1', 'state' => 'La Paz'],
            ['name' => 'San Pedro Masahuat', 'status' => '1', 'state' => 'La Paz'],
            ['name' => 'Santa María Ostuma', 'status' => '1', 'state' => 'La Paz'],
            ['name' => 'Tapalhuaca', 'status' => '1', 'state' => 'La Paz'],
            ['name' => 'Zacatecoluca', 'status' => '1', 'state' => 'La Paz'],
            ['name' => 'Chalchuapa', 'status' => '1', 'state' => 'Santa Ana'],
            ['name' => 'El Congo', 'status' => '1', 'state' => 'Santa Ana'],
            ['name' => 'Metapán', 'status' => '1', 'state' => 'Santa Ana'],
            ['name' => 'Santa Ana', 'status' => '1', 'state' => 'Santa Ana'],
            ['name' => 'San Sebastián Salitrillo', 'status' => '1', 'state' => 'Santa Ana'],
            ['name' => 'Candelaria de la Frontera', 'status' => '1', 'state' => 'Santa Ana'],
            ['name' => 'Coatepeque', 'status' => '1', 'state' => 'Santa Ana'],
            ['name' => 'El Porvenir', 'status' => '1', 'state' => 'Santa Ana'],
            ['name' => 'Masahuat', 'status' => '1', 'state' => 'Santa Ana'],
            ['name' => 'San Antonio Pajonal', 'status' => '1', 'state' => 'Santa Ana'],
            ['name' => 'Santa Rosa Guachipilín', 'status' => '1', 'state' => 'Santa Ana'],
            ['name' => 'Santiago de la Frontera', 'status' => '1', 'state' => 'Santa Ana'],
            ['name' => 'Texistepeque', 'status' => '1', 'state' => 'Santa Ana'],
            ['name' => 'Sonsonate', 'status' => '1', 'state' => 'Sonsonate'],
            ['name' => 'Sonzacate', 'status' => '1', 'state' => 'Sonsonate'],
            ['name' => 'Acajutla', 'status' => '1', 'state' => 'Sonsonate'],
            ['name' => 'Armenia', 'status' => '1', 'state' => 'Sonsonate'],
            ['name' => 'Izalco', 'status' => '1', 'state' => 'Sonsonate'],
            ['name' => 'Juayúa', 'status' => '1', 'state' => 'Sonsonate'],
            ['name' => 'Nahuizalco', 'status' => '1', 'state' => 'Sonsonate'],
            ['name' => 'Salcoatitán', 'status' => '1', 'state' => 'Sonsonate'],
            ['name' => 'Caluco', 'status' => '1', 'state' => 'Sonsonate'],
            ['name' => 'Cuisnahuat', 'status' => '1', 'state' => 'Sonsonate'],
            ['name' => 'Nahulingo', 'status' => '1', 'state' => 'Sonsonate'],
            ['name' => 'San Antonio del Monte', 'status' => '1', 'state' => 'Sonsonate'],
            ['name' => 'San Julián', 'status' => '1', 'state' => 'Sonsonate'],
            ['name' => 'Santa Catarina Masahuat', 'status' => '1', 'state' => 'Sonsonate'],
            ['name' => 'Santa Isabel Ishuatán', 'status' => '1', 'state' => 'Sonsonate'],
            ['name' => 'Santo Domingo de Guzmán', 'status' => '1', 'state' => 'Sonsonate'],
            ['name' => 'Apastepeque', 'status' => '1', 'state' => 'San Vicente'],
            ['name' => 'Santo Domingo', 'status' => '1', 'state' => 'San Vicente'],
            ['name' => 'San Lorenzo', 'status' => '1', 'state' => 'San Vicente'],
            ['name' => 'San Sebastián', 'status' => '1', 'state' => 'San Vicente'],
            ['name' => 'San Vicente', 'status' => '1', 'state' => 'San Vicente'],
            ['name' => 'Tecoluca', 'status' => '1', 'state' => 'San Vicente'],
            ['name' => 'Guadalupe', 'status' => '1', 'state' => 'San Vicente'],
            ['name' => 'San Cayetano Istepeque', 'status' => '1', 'state' => 'San Vicente'],
            ['name' => 'San Esteban Catarina', 'status' => '1', 'state' => 'San Vicente'],
            ['name' => 'San Ildefonso', 'status' => '1', 'state' => 'San Vicente'],
            ['name' => 'Santa Clara', 'status' => '1', 'state' => 'San Vicente'],
            ['name' => 'Tepetitán', 'status' => '1', 'state' => 'San Vicente'],
            ['name' => 'Verapaz', 'status' => '1', 'state' => 'San Vicente'],
            ['name' => 'Conchagua', 'status' => '1', 'state' => 'La Unión'],
            ['name' => 'La Unión', 'status' => '1', 'state' => 'La Unión'],
            ['name' => 'Pasaquina', 'status' => '1', 'state' => 'La Unión'],
            ['name' => 'San Alejo', 'status' => '1', 'state' => 'La Unión'],
            ['name' => 'Santa Rosa de Lima', 'status' => '1', 'state' => 'La Unión'],
            ['name' => 'Anamorós', 'status' => '1', 'state' => 'La Unión'],
            ['name' => 'Bolívar', 'status' => '1', 'state' => 'La Unión'],
            ['name' => 'Concepción de Oriente', 'status' => '1', 'state' => 'La Unión'],
            ['name' => 'El Carmen', 'status' => '1', 'state' => 'La Unión'],
            ['name' => 'El Sauce', 'status' => '1', 'state' => 'La Unión'],
            ['name' => 'Intipucá', 'status' => '1', 'state' => 'La Unión'],
            ['name' => 'Lilisque', 'status' => '1', 'state' => 'La Unión'],
            ['name' => 'Meanguera del Golfo', 'status' => '1', 'state' => 'La Unión'],
            ['name' => 'Nueva Esparta', 'status' => '1', 'state' => 'La Unión'],
            ['name' => 'Polorós', 'status' => '1', 'state' => 'La Unión'],
            ['name' => 'San José', 'status' => '1', 'state' => 'La Unión'],
            ['name' => 'Yayantique', 'status' => '1', 'state' => 'La Unión'],
            ['name' => 'Yucuaiquín', 'status' => '1', 'state' => 'La Unión'],
            ['name' => 'Usulután', 'status' => '1', 'state' => 'Usulután'],
            ['name' => 'Berlín', 'status' => '1', 'state' => 'Usulután'],
            ['name' => 'Jiquilisco', 'status' => '1', 'state' => 'Usulután'],
            ['name' => 'Jucuapa', 'status' => '1', 'state' => 'Usulután'],
            ['name' => 'Mercedes Umaña', 'status' => '1', 'state' => 'Usulután'],
            ['name' => 'Santa Elena', 'status' => '1', 'state' => 'Usulután'],
            ['name' => 'Santa María', 'status' => '1', 'state' => 'Usulután'],
            ['name' => 'Alegría', 'status' => '1', 'state' => 'Usulután'],
            ['name' => 'California', 'status' => '1', 'state' => 'Usulután'],
            ['name' => 'Concepción Batres', 'status' => '1', 'state' => 'Usulután'],
            ['name' => 'El Triunfo', 'status' => '1', 'state' => 'Usulután'],
            ['name' => 'Ereguayquín', 'status' => '1', 'state' => 'Usulután'],
            ['name' => 'Estanzuelas', 'status' => '1', 'state' => 'Usulután'],
            ['name' => 'Jucuarán', 'status' => '1', 'state' => 'Usulután'],
            ['name' => 'Nueva Granada', 'status' => '1', 'state' => 'Usulután'],
            ['name' => 'Ozatlán', 'status' => '1', 'state' => 'Usulután'],
            ['name' => 'San Agustín', 'status' => '1', 'state' => 'Usulután'],
            ['name' => 'San Buenaventura', 'status' => '1', 'state' => 'Usulután'],
            ['name' => 'San Dionisio', 'status' => '1', 'state' => 'Usulután'],
            ['name' => 'San Francisco Javier', 'status' => '1', 'state' => 'Usulután'],
            ['name' => 'Santiago de María', 'status' => '1', 'state' => 'Usulután'],
            ['name' => 'Tecapán', 'status' => '1', 'state' => 'Usulután'],
            ['name' => 'Puerto El Triunfo', 'status' => '1', 'state' => 'Usulután']            
        ];

        $states = State::where('business_id', $business_id)
            ->select('id', 'name')->get();

        foreach ($cities as $c) {
            $state = $states->where('name', $c['state'])->first();

            City::updateOrCreate(
                [
                    'name' => $c['name'],
                    'state_id' => $state->id,
                    'business_id' => $business_id
                ],
                [
                    'status' => $c['status']
                ]
            );
        }
    }
}
