use carparkapp;

-- CREATE TESTDATA
INSERT INTO user_role (name)
VALUES 
	('Administrator'),
    ('Kundenservice'),
    ('Parkhaus'),
    ('Kunde');
    
INSERT INTO carpark (name, street, street_number, zip_code, place)
VALUES 
	('Parkhaus Groner Tor', 'Groner-Tor-Straße', '31', '37075', 'Göttingen'),
    ('Parkhaus Hospitalstraße', 'Hospital Straße', '8', '37075', 'Göttingen');

