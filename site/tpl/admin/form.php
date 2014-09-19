<form action="#" method="post">
    <fieldset>
        <legend>表单组1</legend>
        <label for="i1">请输入你的姓名</label>
        <input type="text" name="i1" id="i1" placeholder="hahah"/>
        <br/>
        <label for="i2">你的邮箱</label>
        <input type="email" name="i2" id="i2" value="123"/>
        <br/>
        <label for="i3">地址</label>
        <input type="url" name="i3" id="i3" value="123"/>
        <br/>
        <label for="i4">密码</label>
        <input type="password" name="i4" id="i4" value="123"/>
    </fieldset>
    <fieldset>
        <legend>表单组2</legend>
        <label for="i5">i1:</label>
        <input type="file" name="i5" id="i5" value="123"/>
        <br/>
        <label for="i8">i1:</label>
        <input type="checkbox" name="i8" id="i8" value="123"/>
    </fieldset>
    <fieldset>
        <legend>表单组3</legend>
        <label for="i9">i1:</label>
        <input type="radio" name="i9" id="i9"/>
        <label for="i10">i2:</label>
        <input type="radio" name="i9" id="i10"/>
        <label for="i11">i3:</label>
        <input type="radio" name="i9" id="i11"/>
        <label for="i12">i1:</label>
        <input type="radio" name="i9" id="i12"/>
    </fieldset>
    <fieldset>
        <legend>表单组4</legend>
        <label for="select1">i1:</label>
        <select name="select" id="select1">
            <option>依然</option>
            <option>阳辉</option>
            <option>松高</option>
        </select>
    </fieldset>
    <fieldset>
        <legend>文本</legend>
        <label for="i13">i1:</label>
        <textarea id="i13">12312313</textarea>
    </fieldset>
    <fieldset>
        <legend>文本</legend>
        <script id="aTextInput" name="aTextInput" class="editor" type="text/plain"></script>
    </fieldset>
    <input type="submit"/>
</form>