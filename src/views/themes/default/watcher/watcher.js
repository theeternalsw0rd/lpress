var watch = require('watch'),
    path = require('path'),
    coffee = require('coffee-script'),
    uglifyjs = require('uglify-js'),
    sass = require('node-sass'),
    fs = require('fs'),
    end_of_line = require('os').EOL,
    root = path.dirname(__dirname);
var coffee_src = path.resolve(root, 'coffee'),
    js_uncompressed = path.resolve(root, 'assets/js/uncompressed'),
    js_compressed = path.resolve(root, 'assets/js/compressed');

var compileCoffee = function(source, uncompressed_file, compressed_file, type) {
    if(source == '') {
        console.log('Source was empty, aborting');
        return;
    }
    var compiled, minified;
    switch(type) {
        case 'jquery.ready': {
            compiled = 'jQuery(document).ready(function() {' + coffee.compile(source) + '});';
        }
        default: {
            compiled = coffee.compile(source);
        }
    }
    fs.writeFile(uncompressed_file, compiled, function(err) {
        if(err) throw err;
        console.log('coffee compiled to ' + uncompressed_file)
    });
    minified = uglifyjs.minify(compiled, {fromString: true});
    fs.writeFile(compressed_file, minified.code, function(err) {
        if(err) throw err;
        console.log('coffee minified to ' + compressed_file);
    });
}

watch.createMonitor(coffee_src, function(monitor) {
    monitor.on("changed", function (f, curr, prev) {
        var extension, filename, lines, pack,
            basename, brew, scripts, source;
        extension = path.extname(f);
        if(extension != '.coffee') return;
        filename = path.basename(f, '.coffee') + '.js';
        fs.readFile(f, 'utf8', function (err, data) {
            if(err) {
                return console.log(err);
            }
            lines = data.split("\n");
            pack = lines[0].match(/package\:([A-z\d\-\_\.]*)/) || [];
            if(pack.length > 0) {
                basename = pack[1];
                filename = basename + '.js';
                console.log('compiling package ' + basename);
                console.log('reading brew configuration...');
                try {
                    brew = fs.readFileSync(path.resolve(coffee_src, 'brew.json'), 'utf8');
                } catch(e) {
                    console.log('Could not read brew.json file from coffee directory. Aborting compilation.');
                    return;
                }
                brew = JSON.parse(brew);
                brew = brew.packages[basename];
                if(brew === undefined) {
                    console.log('Could not find package ' + basename + ' in brew.json. Aborting compilation.');
                    return;
                }
                scripts = brew.requires;
                source = '';
                for(i=0, script_count=scripts.length; i<script_count; i++) {
                    try {
                        source += fs.readFileSync(path.resolve(coffee_src, scripts[i]), 'utf8') + end_of_line;
                    } catch(e) {
                        console.log('Could not find required file ' + scripts[i] + ' so aborting compilation.');
                        return;
                    }
                }
                compileCoffee(
                    source,
                    path.resolve(js_uncompressed, filename),
                    path.resolve(js_compressed, filename),
                    brew.type
                );
                return;
            }
            compileCoffee(
                data,
                path.resolve(js_uncompressed, filename),
                path.resolve(js_compressed, filename),
                'normal'
            );
        });
    });
});
