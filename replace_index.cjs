const fs = require('fs');

const files = [
    {
        path: './resources/js/Pages/Admin/Student/Index.vue',
        listName: 'students',
        itemVar: 'student'
    },
    {
        path: './resources/js/Pages/Admin/Teacher/Index.vue',
        listName: 'teachers',
        itemVar: 'teacher'
    },
    {
        path: './resources/js/Pages/Admin/Level/Index.vue',
        listName: 'levels',
        itemVar: 'level'
    },
    {
        path: './resources/js/Pages/Admin/Group/Index.vue',
        listName: 'groups',
        itemVar: 'group'
    },
    {
        path: './resources/js/Pages/Admin/CategoryGroup/Index.vue',
        listName: 'categories',
        itemVar: 'category'
    },
    {
        path: './resources/js/Pages/Admin/HistoryMovement/Index.vue',
        listName: 'movements',
        itemVar: 'movement'
    },
    {
        path: './resources/js/Pages/Admin/Attending/Index.vue',
        listName: 'attendings',
        itemVar: 'attending'
    }
];

files.forEach(f => {
    if (!fs.existsSync(f.path)) return;
    let content = fs.readFileSync(f.path, 'utf8');
    
    // Replace v-for="item in list?.data"
    const regexFor = new RegExp(`v-for="${f.itemVar} in ${f.listName}\\?\\.data"`, 'g');
    content = content.replace(regexFor, `v-for="(${f.itemVar}, index) in ${f.listName}?.data"`);

    // Replace {{ item.id }} directly under <td data-label="N°">
    const regexId = new RegExp(`<td data-label="N°">\\s*{{\\s*${f.itemVar}\\.id\\s*}}\\s*<\\/td>`, 'g');
    content = content.replace(regexId, `<td data-label="N°">\n                {{ (${f.listName}.current_page - 1) * ${f.listName}.per_page + index + 1 }}\n              </td>`);

    fs.writeFileSync(f.path, content, 'utf8');
    console.log('Updated ' + f.path);
});

// Now for non-paginated lists in Show / Edit views
const simpleFiles = [
    {
        path: './resources/js/Pages/Admin/Group/Show.vue',
        listName: 'group_students',
        itemVar: 'student'
    },
    {
        path: './resources/js/Pages/Admin/HistoryMovement/Show.vue',
        listName: 'batchMovements',
        itemVar: 'm'
    },
    {
        path: './resources/js/Pages/Admin/HistoryMovement/Edit.vue',
        listName: 'batchMovements',
        itemVar: 'm'
    },
    {
        path: './resources/js/Pages/Admin/Attending/Edit.vue',
        listName: 'form.attendances',
        itemVar: 'attendance'
    }
];

simpleFiles.forEach(f => {
    if (!fs.existsSync(f.path)) return;
    let content = fs.readFileSync(f.path, 'utf8');
    
    // Replace v-for="item in list" or v-for="(item, idx) in list"
    // Since some already have index (Attending/Edit.vue has v-for="(attendance, index) in form.attendances")
    if (!content.includes(`(${f.itemVar}, index) in ${f.listName}`)) {
        const regexFor = new RegExp(`v-for="${f.itemVar} in ${f.listName}"`, 'g');
        content = content.replace(regexFor, `v-for="(${f.itemVar}, index) in ${f.listName}"`);
    }

    // Replace <td class="p-4">{{ item.id }}</td>
    // We can just find {{ item.id }} and replace with {{ index + 1 }}
    // In some files, it's `student.id` or `m.student.id`
    if (f.path.includes('HistoryMovement')) {
        content = content.replace(/{{ m\.student\.id }}/g, '{{ index + 1 }}');
    } else if (f.path.includes('Group/Show')) {
        content = content.replace(/{{ student\.id }}/g, '{{ index + 1 }}');
    } else if (f.path.includes('Attending/Edit')) {
        content = content.replace(/{{ attendance\.identity_id }}/g, '{{ index + 1 }}');
    }

    fs.writeFileSync(f.path, content, 'utf8');
    console.log('Updated simple list ' + f.path);
});
