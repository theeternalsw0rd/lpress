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
	'xpSha2' => 'It appears you are using Windows XP. The secure portions of this website requires SHA2 support which is not available in your browser. Only <a href="http://www.mozilla.com/firefox">Mozilla Firefox</a> has been verified to work under Windows XP.',
	'loginFailed' => 'Login failed, please try again.',
	'permissionError' => 'Permission denied. Your user does not have access to this content.',
	'executePermissionError' => 'Permission denied. Your user does not have the privileges to execute this action.',
	'pathPermissionError' => 'Permission denied. Relative paths are not allowed.',
	'mimePermissionError' => ':mime is not allowed by this server.',
	'assetNotFound' => 'Could not find asset file.',
	'attachmentMissing' => 'Record was found, but filename value is missing.',
	'invalidURL' => 'No records could be found for this url.',
	'templateMissing' => 'No template could be found for this RecordType.',
	'saveFailed' => 'Some data was not saved to the database.'
);
