var watch = require('watch'),
    path = require('path'),
    coffee = require('coffee-script'),
    uglifyjs = require('uglify-js'),
    fs = require('fs'),
    find = require('findit').find,
    exec_real = require('child_process').exec,
    end_of_line = require('os').EOL,
    root = path.dirname(__dirname),
    coffee_src = path.resolve(root, 'coffee'),
    js_uncompressed = path.resolve(root, 'assets/js/uncompressed'),
    js_compressed = path.resolve(root, 'assets/js/compressed'),
    sass_src = path.resolve(root, 'sass'),
    css_uncompressed = path.resolve(root, 'assets/css/uncompressed'),
    css_compressed = path.resolve(root, 'assets/css/compressed'),
    sass_last_save_file = '',
    sass_last_save_time = '';

var mkdirp = function(real_path, mode, callback) {
    process.nextTick(function() {
        var path_from_root = real_path.split(root)[1].substr(1),
            path_array = path_from_root.split(path.sep),
            create_path = root;
        for(var i=0, path_count=path_array.length;i<path_count;i++) {
            segment = path_array[i];
            create_path = path.resolve(create_path, segment);
            if(!fs.existsSync(create_path)) {
                try {
                    fs.mkdirSync(create_path, mode);
                } catch(e) {
                    return callback(e);
                }
                console.log('mkdir: ' + create_path);
            }
        }
        callback(null);
    });
}

var compiledCoffee = function(source, type) {
    var compiled;
    switch(type) {
        case 'jquery.ready': {
            compiled = 'jQuery(document).ready(function( $ ) {' + coffee.compile(source) + '});';
            break;
        }
        default: {
            compiled = coffee.compile(source);
        }
    }
    return compiled;
}

var uncompressedCoffee = function(source, filename, uncompressed_path) {
    var uncompressed_file = path.resolve(uncompressed_path, filename),
        uncompressed_file_label = uncompressed_file.split(root)[1];
    fs.writeFile(uncompressed_file, source, function(err) {
        if(err) throw err;
        console.log('coffee compiled to ' + uncompressed_file_label);
    });
}

var compressedCoffee = function(source, filename, compressed_path) {
    var compressed_file = path.resolve(compressed_path, filename),
        minified = uglifyjs.minify(source, {fromString: true}),
        compressed_file_label = compressed_file.split(root)[1];
    fs.writeFile(compressed_file, minified.code, function(err) {
        if(err) throw err;
        console.log('coffee minified to ' + compressed_file_label);
    });
}

var stirCoffee = function(scripts, callback) {
    process.nextTick(function() {
        var source = '';
        for(var i=0, script_count=scripts.length; i<script_count; i++) {
            try {
                source += fs.readFileSync(path.resolve(coffee_src, scripts[i]), 'utf8') + end_of_line;
            } catch(e) {
                return callback(e, null);
            }
        }
        callback(null, source);
    });
}

var compileCoffee = function(pack_name) {
    var segments, basename, relative_path = '', filename, pack,
        scripts, uncompressed_path, compressed_path;
    segments = pack_name.split('/');
    for(var i=0, segment_max=segments.length - 1;i<=segment_max;i++) {
        if(i == 0) relative_path += segments[i];
        if(i > 0 && i < segment_max) relative_path += path.sep + segments[i];
        if(i == segment_max) basename = segments[i];
    }
    filename = basename + '.js';
    console.log('brewing coffee package ' + pack_name);
    console.log('reading brew configuration...');
    fs.readFile(path.resolve(coffee_src, 'brew.json'), 'utf8', function(err, data) {
        if(err) return console.log('Could not read brew.json file from coffee directory. Aborting compilation.');
        brew = JSON.parse(data);
        pack = brew.packages[pack_name];
        if(pack === undefined) {
            console.log('Could not find package ' + pack_name + ' in brew.json. Aborting compilation.');
            return;
        }
        stirCoffee(pack.requires, function(err, data) {
            if(err) return console.log(err);
            uncompressed_path = path.resolve(js_uncompressed, relative_path);
            compressed_path = path.resolve(js_compressed, relative_path);
            mkdirp(uncompressed_path, 0750, function(err) {
                if(err) throw err;
                uncompressedCoffee(
                    compiledCoffee(data, pack.type),
                    filename,
                    uncompressed_path
                );
            });
            mkdirp(compressed_path, 0750, function(err) {
                if(err) throw err;
                compressedCoffee(
                    compiledCoffee(data, pack.type),
                    filename,
                    compressed_path
                );
            });
        });
    });
}

var exec = function(command) {
    exec_real(command, function(error, stdout, stderr) {
        if(stdout !== '') console.log('stdout: ' + stdout);
        if(stderr !== '') console.log('stderr: ' + stderr);
    });
}

var compressedSass = function(file, filename, real_path, type) {
    var file_label, compressed_file, compressed_file_label;
    compressed_file = path.resolve(real_path, filename);
    file_label = file.split(root)[1];
    compressed_file_label = compressed_file.split(root)[1];
    console.log('compiling ' + file_label + ' compressed to ' + compressed_file_label);
    if(type == '.sass') exec('sass ' + file + ' -t compressed ' + compressed_file);
    else { exec('sass --scss ' + file + ' ' + compressed_file); }
}

var uncompressedSass = function(file, filename, real_path, type) {
    var file_label, uncompressed_file, uncompressed_file_label;
    uncompressed_file = path.resolve(real_path, filename);
    file_label = file.split(root)[1];
    uncompressed_file_label = uncompressed_file.split(root)[1];
    console.log('compiling ' + file_label + ' uncompressed to ' + uncompressed_file_label);
    if(type == '.sass') exec('sass ' + file + ' ' + uncompressed_file);
    else { exec('sass --scss ' + file + ' ' + uncompressed_file); }
}

var compileSass = function(file, partial) {
    var filename, basename, extension, relative_path,
        uncompressed_path, compressed_path;
    extension = path.extname(file);
    basename = path.basename(file, extension);
    filename = basename + '.css';
    relative_path = path.dirname(file).split(sass_src)[1].substr(1);
    uncompressed_path = path.resolve(css_uncompressed, relative_path);
    compressed_path = path.resolve(css_compressed, relative_path);
    mkdirp(uncompressed_path, 0750, function(err) {
        if(err) throw err;
        uncompressedSass(
            file,
            filename,
            uncompressed_path,
            extension
        );
    });
    mkdirp(compressed_path, 0750, function(err) {
        if(err) throw err;
        compressedSass(
            file,
            filename,
            compressed_path,
            extension
        );
    });
}

var processSass = function(file, partial) {
    fs.readFile(file, 'utf8', function(err, data) {
        var haystack, match, partial_root, match_name, partial_name;
        if(err) {
            console.log('Failed to process due to following error:');
            return console.log(err);
        }
        partial_name = path.basename(partial, path.extname(partial)).substr(1);
        haystack = new RegExp('@import +"([^"]*)"', 'g');
        while(match = haystack.exec(data)) {
            match = match[1];
            // imports are relative so use resolve on the match and file's parent
            partial_root = path.dirname(path.resolve(path.dirname(file), match));
            // just because the partial's parent matches doesn't mean the partial does
            match_name = match.split('/');
            match_name = match_name[match_name.length - 1];
            if(partial_root == path.dirname(partial) && match_name == partial_name) {
                compileSass(file, partial);
                break;
            }
        }
    });
}

watch.createMonitor(sass_src, function(monitor) {
    monitor.on("changed", function(f, curr, prev) {
        var extension, filename, lines, basename, relative_path,
            uncompressed_path, compressed_path;
        if(sass_last_save_file == f && sass_last_save_time == curr.mtime) return;
        sass_last_save_file = f;
        sass_last_save_time = curr.mtime;
        extension = path.extname(f);
        if(extension != '.sass' && extension != '.scss') return;
        basename = path.basename(f, extension);
        if(basename.charAt(0) == '_') {
            finder = find(sass_src);
            finder.on('file', function(file, stat) {
                var extension, filename, basename;
                extension = path.extname(file);
                basename = path.basename(file, extension);
                if(basename.charAt(0) == '_' || (extension != '.sass' && extension != '.scss')) {
                    return;
                }
                processSass(file, f);
            });
            return;
        }
        filename = basename + '.css';
        relative_path = path.dirname(f).split(sass_src)[1].substr(1);
        uncompressed_path = path.resolve(css_uncompressed, relative_path);
        compressed_path = path.resolve(css_compressed, relative_path);
        mkdirp(uncompressed_path, 0750, function(err) {
            if(err) throw err;
            uncompressedSass(
                f,
                filename,
                uncompressed_path,
                extension
            );
        });
        mkdirp(compressed_path, 0750, function(err) {
            if(err) throw err;
            compressedSass(
                f,
                filename,
                compressed_path,
                extension
            );
        });
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
                console.log('Failed to process file due to following error:');
                return console.log(err);
            }
            lines = data.split("\n");
            pack = lines[0].match(/package\:([A-z\d\-\_\.\/]*)/) || [];
            if(pack.length > 0) {
                compileCoffee(pack[1]);
                return;
            }
            console.log('The following coffeescript is not set up for packaging: ');
            console.log(f);
        });
    });
});
