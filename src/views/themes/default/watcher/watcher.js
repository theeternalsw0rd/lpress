var watch = require('watch'),
    path = require('path'),
    coffee = require('coffee-script'),
    uglifyjs = require('uglify-js'),
    fs = require('fs'),
    find = require('findit').sync,
    exec_real = require('child_process').exec,
    end_of_line = require('os').EOL,
    root = path.dirname(__dirname);
var coffee_src = path.resolve(root, 'coffee'),
    js_uncompressed = path.resolve(root, 'assets/js/uncompressed'),
    js_compressed = path.resolve(root, 'assets/js/compressed'),
    sass_src = path.resolve(root, 'sass'),
    css_uncompressed = path.resolve(root, 'assets/css/uncompressed'),
    css_compressed = path.resolve(root, 'assets/css/compressed'),
    sass_last_save_file = '',
    sass_last_save_time = '';

var compileCoffee = function(source, uncompressed_file, compressed_file, type) {
    if(source == '') {
        console.log('Source was empty, aborting');
        return;
    }
    var compiled, minified;
    switch(type) {
        case 'jquery.ready': {
            compiled = 'jQuery(document).ready(function( $ ) {' + coffee.compile(source) + '});';
            break;
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

var exec = function(command) {
    exec_real(command, function(error, stdout, stderr) {
        if(stdout !== '') console.log('stdout: ' + stdout);
        if(stderr !== '') console.log('stderr: ' + stderr);
        if(error !== null) console.log('exec error: ' + error);
    });
}

var compileSass = function(file, uncompressed_file, compressed_file, type) {
    var file_label, uncompressed_file_label, compressed_file_label;
    file_label = file.split(root)[1];
    uncompressed_file_label = uncompressed_file.split(root)[1];
    compressed_file_label = compressed_file.split(root)[1];
    console.log('compiling ' + file_label + ' uncompressed to ' + uncompressed_file_label);
    if(type == '.sass') exec('sass ' + file + ' ' + uncompressed_file);
    else { exec('sass --scss ' + file + ' ' + uncompressed_file); }
    console.log('compiling ' + file_label + ' compressed to ' + compressed_file_label);
    if(type == '.sass') exec('sass ' + file + ' -t compressed ' + compressed_file);
    else { exec('sass --scss ' + file + ' ' + compressed_file); }
}

var getSassFiles = function() {
    var keep = [], files = find(sass_src), filename, file;
    for(var i=0, file_count=files.length;i<file_count;i++) {
        file = files[i];
        filename = path.basename(files[i]);
        extension = path.extname(filename);
        if(filename.charAt(0) != '_' && (extension == '.sass' || extension == '.scss')) {
            keep.push(file);
        }
    }
    return keep;
}

var getSassImports = function(files, basename) {
    var keep = [], matches = [], lines, file, line,
        haystack = '@import "[^/]*\\/?' + basename + '"', regex = new RegExp(haystack);
    for(var i=0, file_count=files.length;i<file_count;i++) {
        file = files[i];
        try {
            lines = fs.readFileSync(file, 'utf8');
        } catch(e) {
            console.log('Aborting sass compilation due to the following error:');
            console.log(e);
            return;
        }
        matches = lines.match(regex);
        if(!matches) continue;
        keep.push(file);
    }
    return keep;
}

watch.createMonitor(sass_src, function(monitor) {
    monitor.on("changed", function(f, curr, prev) {
        var extension, filename, files, file, lines, basename;
        if(sass_last_save_file == f && sass_last_save_time == curr.mtime) return;
        sass_last_save_file = f;
        sass_last_save_time = curr.mtime;
        extension = path.extname(f);
        if(extension != '.sass' && extension != '.scss') return;
        basename = path.basename(f, extension);
        if(basename.charAt(0) == '_') {
            files = getSassFiles();
            files = getSassImports(files, basename.split('_')[1]);
            for(var i=0, file_count=files.length;i<file_count;i++) {
                file = files[0];
                extension = path.extname(file);
                basename = path.basename(file, extension);
                filename = basename + '.css';
                compileSass(
                    file,
                    path.resolve(css_uncompressed, filename),
                    path.resolve(css_compressed, filename),
                    extension
                );
            }
            return;
        }
        filename = basename + '.css';
        compileSass(
            f,
            path.resolve(css_uncompressed, filename),
            path.resolve(css_compressed, filename),
            extension
        );
    });
});

watch.createMonitor(coffee_src, function(monitor) {
    monitor.on("changed", function(f, curr, prev) {
        var extension, filename, lines, pack,
            basename, brew, scripts, source;
        extension = path.extname(f);
        if(extension != '.coffee') return;
        filename = path.basename(f, extension) + '.js';
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
