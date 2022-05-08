CREATE TABLE IF NOT EXISTS transactions(
txid int PRIMARY KEY,
txpayload text,
username text,
signature text,
signerPublicKey text,
signingKeyAlgorithm text,
signingKeyType text,
authenticatorData text,
clientDataJson text);