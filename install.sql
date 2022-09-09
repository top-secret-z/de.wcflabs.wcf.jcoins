DROP TABLE IF EXISTS wcf1_jcoins_statement;
CREATE TABLE wcf1_jcoins_statement (
	statementID				INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	userID 					INT(10) NOT NULL,
	time 					INT(10) NOT NULL DEFAULT 0,
	objectTypeID			INT(10) NOT NULL,
	objectID				INT(10),
	additionalData			MEDIUMTEXT,
	amount 					INT(10) NOT NULL DEFAULT 0,
	isTrashed				BOOLEAN NOT NULL DEFAULT 0,
	KEY user (userID),
	KEY (objectTypeID),
	KEY user_time (userID, time),
	KEY (time)
);

DROP TABLE IF EXISTS wcf1_purchasable_jcoins;
CREATE TABLE wcf1_purchasable_jcoins (
	purchasableJCoinsID		INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	title					VARCHAR(255) NOT NULL DEFAULT '',
	description				TEXT,
	isDisabled				TINYINT(1) NOT NULL DEFAULT 0,
	showOrder				INT(10) NOT NULL DEFAULT 0,
	cost					DECIMAL(10,2) NOT NULL DEFAULT 0,
	currency				VARCHAR(3) NOT NULL DEFAULT 'EUR',
	jCoins					INT(10) NOT NULL DEFAULT 0,
	availableUntil			INT(10) NOT NULL DEFAULT 0, 
	useHTML					TINYINT(1) NOT NULL DEFAULT 0
);

DROP TABLE IF EXISTS wcf1_purchasable_jcoins_transaction_log;
CREATE TABLE wcf1_purchasable_jcoins_transaction_log (
	logID						INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	userID						INT(10),
	purchasableJCoinsID			INT(10),
	paymentMethodObjectTypeID	INT(10) NOT NULL,
	logTime						INT(10) NOT NULL DEFAULT 0,
	transactionID				VARCHAR(255) NOT NULL DEFAULT '',
	transactionDetails			MEDIUMTEXT,
	logMessage					VARCHAR(255) NOT NULL DEFAULT ''
);

ALTER TABLE wcf1_jcoins_statement ADD FOREIGN KEY (objectTypeID) REFERENCES wcf1_object_type (objectTypeID) ON DELETE CASCADE;
ALTER TABLE wcf1_jcoins_statement ADD FOREIGN KEY (userID) REFERENCES wcf1_user (userID) ON DELETE CASCADE;

ALTER TABLE wcf1_purchasable_jcoins_transaction_log ADD FOREIGN KEY (userID) REFERENCES wcf1_user (userID) ON DELETE SET NULL;
ALTER TABLE wcf1_purchasable_jcoins_transaction_log ADD FOREIGN KEY (purchasableJCoinsID) REFERENCES wcf1_purchasable_jcoins (purchasableJCoinsID) ON DELETE SET NULL;
ALTER TABLE wcf1_purchasable_jcoins_transaction_log ADD FOREIGN KEY (paymentMethodObjectTypeID) REFERENCES wcf1_object_type (objectTypeID) ON DELETE CASCADE;
