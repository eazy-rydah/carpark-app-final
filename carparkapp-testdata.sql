use carparkapp;

-- CREATE TESTDATA
INSERT INTO user_role (name)
VALUES 
	('Administrator'),
    ('Employee_Customer_Service'),
    ('Employee_Carpark'),
    ('Client');
    
INSERT INTO carpark (name, street, street_number, zip_code, place)
VALUES 
	('Parkhaus Groner Tor', 'Groner-Tor-Straße', '31', '37075', 'Göttingen'),
    ('Parkhaus Hospitalstraße', 'Hospital Straße', '8', '37075', 'Göttingen');

INSERT INTO user (first_name, last_name, email, password_hash, user_role_id, is_active)
VALUES 
	('admin', 'admin', 'admin@swgoe.de', 'admin123', 1, 1),
    ('kundenservice', 'kundenservice', 'kundenservice@swgoe.de', 'kundenservice123', 2, 1),
    ('parkhaus', 'parkhaus', 'parkhaus@swgoe.de', 'parkhaus123', 3, 1),
    ('kunde', 'kunde', 'kunde@swgoe.de', 'kunde123', 4, 1);
    
