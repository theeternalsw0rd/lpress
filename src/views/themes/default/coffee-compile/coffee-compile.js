var watch = require('watch'),
    path = require('path'),
    coffee = require('coffee-script'),
    minify = require('uglify-js'),
    fs = require('fs'),
    root = path.dirname(__dirname);
var coffee_src = path.resolve(root, process.argv[2]),
    uncompressed = path.resolve(root, process.argv[3]),
    compressed = path.resolve(root, process.argv[4]);

watch.createMonitor(coffee_src, function(monitor) {
    monitor.on("changed", function (f, curr, prev) {
        var extension = path.extname(f);
	if(extension != '.coffee') return;
	var filename = path.basename(f, '.coffee') + '.js';
        fs.readFile(f, "ascii", function (err, data) {
            if(err) {
                return console.log(err);
	    }
	    var compiled = coffee.compile(data);
	    var uncompressed_file = path.resolve(uncompressed, filename);
	    var compressed_file = path.resolve(compressed, filename);
	    fs.writeFile(uncompressed_file, compiled, function(err) {
                if(err) throw err;
		console.log('coffee compiled to ' + uncompressed_file);
	    });
	    var minified = minify.minify(compiled, {fromString: true});
	    fs.writeFile(compressed_file, minified.code, function(err) {
                if(err) throw err;
		console.log('coffee minified to ' + compressed_file);
	    });
	});
    });
});
