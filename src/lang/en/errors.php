<?php

return array(
	'httpStatus500' => 'An unknown error occurred and your request could not be processed.',
	'tokenMismatch' => 'Permission denied. Tokens do not match.',
	'ajaxNotLoggedIn' => 'Permission denied. Not logged in.',
	'modelIdNotFound' => 'Could not find model with id :id. It\'s possible it was deleted by another user or the posted data was corrupt.',
	'missingRecordType' => 'RecordType :slug is missing for this input.',
	'invalidRecordType' => 'RecordType :slug is not valid for this input.',
	'dbConnectionError' => 'Could not connect to database.',
	'dbTableMissing' => 'Could not find :table table in the database, please ensure all migrations have been run.',
	'siteMissing' => 'No valid site found for this domain, if this is not on purpose you may need to seed the database, or you have inadvertantly removed the wildcard domain site.',
	'xpSha2' => 'It appears you are using Windows XP. The secure portions of this website requires SHA2 support which is not available in your browser. Only <a href="http://www.mozilla.com/firefox">Mozilla Firefox</a> has been verified to work under Windows XP.'
);
