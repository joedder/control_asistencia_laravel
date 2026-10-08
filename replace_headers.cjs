const fs = require('fs');
const path = require('path');

function walk(dir) {
    let results = [];
    const list = fs.readdirSync(dir);
    list.forEach(function(file) {
        file = dir + '/' + file;
        const stat = fs.statSync(file);
        if (stat && stat.isDirectory()) { 
            results = results.concat(walk(file));
        } else { 
            if (file.endsWith('.vue')) {
                results.push(file);
            }
        }
    });
    return results;
}

const files = walk('./resources/js/Pages/Admin');

files.forEach(file => {
    let content = fs.readFileSync(file, 'utf8');
    let changed = false;

    // Replace <th>ID</th>
    if (content.includes('<th>ID</th>')) {
        content = content.replace(/<th>ID<\/th>/g, '<th>N°</th>');
        changed = true;
    }
    // Replace <th class="p-4 border-b">ID</th>
    if (content.includes('<th class="p-4 border-b">ID</th>')) {
        content = content.replace(/<th class="p-4 border-b">ID<\/th>/g, '<th class="p-4 border-b">N°</th>');
        changed = true;
    }
    // Replace <td data-label="ID">
    if (content.includes('<td data-label="ID">')) {
        content = content.replace(/<td data-label="ID">/g, '<td data-label="N°">');
        changed = true;
    }
    // Replace Identity ID -> Identification
    if (content.includes('Identity ID')) {
        content = content.replace(/Identity ID/g, 'Identification');
        changed = true;
    }
    // Replace identity_id placeholder
    if (content.includes('Enter Identity ID')) {
        content = content.replace(/Enter Identity ID/g, 'Enter Identification');
        changed = true;
    }
    // Replace any remaining >ID<
    // Replace student ID column in Attending/Edit.vue (it uses ID for the identity_id column)
    if (content.includes('<th class="p-4 border-b">ID</th>')) {
        content = content.replace(/<th class="p-4 border-b">ID<\/th>/g, '<th class="p-4 border-b">N°</th>');
        changed = true;
    }
    
    if (changed) {
        fs.writeFileSync(file, content, 'utf8');
        console.log('Updated ' + file);
    }
});
