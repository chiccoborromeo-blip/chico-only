<Global.Microsoft.VisualBasic.CompilerServices.DesignerGenerated()>
Partial Class frmAdmindashboard
    Inherits System.Windows.Forms.Form

    'Form overrides dispose to clean up the component list.
    <System.Diagnostics.DebuggerNonUserCode()>
    Protected Overrides Sub Dispose(ByVal disposing As Boolean)
        Try
            If disposing AndAlso components IsNot Nothing Then
                components.Dispose()
            End If
        Finally
            MyBase.Dispose(disposing)
        End Try
    End Sub

    'Required by the Windows Form Designer
    Private components As System.ComponentModel.IContainer

    'NOTE: The following procedure is required by the Windows Form Designer
    'It can be modified using the Windows Form Designer.  
    'Do not modify it using the code editor.
    <System.Diagnostics.DebuggerStepThrough()>
    Private Sub InitializeComponent()
        Dim resources As System.ComponentModel.ComponentResourceManager = New System.ComponentModel.ComponentResourceManager(GetType(frmAdmindashboard))
        Me.Label4 = New System.Windows.Forms.Label()
        Me.Panel1 = New System.Windows.Forms.Panel()
        Me.btnLogin = New System.Windows.Forms.Button()
        Me.PictureBox1 = New System.Windows.Forms.PictureBox()
        Me.Label11 = New System.Windows.Forms.Label()
        Me.Label1 = New System.Windows.Forms.Label()
        Me.Panel8 = New System.Windows.Forms.Panel()
        Me.Panel3 = New System.Windows.Forms.Panel()
        Me.btntimesbooksreturned = New System.Windows.Forms.Button()
        Me.Panel11 = New System.Windows.Forms.Panel()
        Me.btnRegistereduser = New System.Windows.Forms.Button()
        Me.Panel9 = New System.Windows.Forms.Panel()
        Me.btnBooklisted = New System.Windows.Forms.Button()
        Me.Panel12 = New System.Windows.Forms.Panel()
        Me.btnReturnrequests = New System.Windows.Forms.Button()
        Me.Panel10 = New System.Windows.Forms.Panel()
        Me.btnBorrowrequests = New System.Windows.Forms.Button()
        Me.gbList = New System.Windows.Forms.GroupBox()
        Me.dgvLoadbooks = New System.Windows.Forms.DataGridView()
        Me.txtSearch = New System.Windows.Forms.TextBox()
        Me.Label2 = New System.Windows.Forms.Label()
        Me.Label3 = New System.Windows.Forms.Label()
        Me.btnAddbooks = New System.Windows.Forms.Button()
        Me.btnAdduser = New System.Windows.Forms.Button()
        Me.Panel1.SuspendLayout()
        CType(Me.PictureBox1, System.ComponentModel.ISupportInitialize).BeginInit()
        Me.Panel8.SuspendLayout()
        Me.Panel3.SuspendLayout()
        Me.Panel11.SuspendLayout()
        Me.Panel9.SuspendLayout()
        Me.Panel12.SuspendLayout()
        Me.Panel10.SuspendLayout()
        Me.gbList.SuspendLayout()
        CType(Me.dgvLoadbooks, System.ComponentModel.ISupportInitialize).BeginInit()
        Me.SuspendLayout()
        '
        'Label4
        '
        Me.Label4.AutoSize = True
        Me.Label4.BackColor = System.Drawing.Color.Transparent
        Me.Label4.Font = New System.Drawing.Font("Segoe UI", 8.0!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.Label4.ForeColor = System.Drawing.Color.Black
        Me.Label4.Location = New System.Drawing.Point(110, 39)
        Me.Label4.Name = "Label4"
        Me.Label4.Size = New System.Drawing.Size(109, 19)
        Me.Label4.TabIndex = 16
        Me.Label4.Text = "Library overview"
        '
        'Panel1
        '
        Me.Panel1.BackColor = System.Drawing.Color.White
        Me.Panel1.Controls.Add(Me.btnLogin)
        Me.Panel1.Controls.Add(Me.PictureBox1)
        Me.Panel1.Controls.Add(Me.Label11)
        Me.Panel1.Controls.Add(Me.Label4)
        Me.Panel1.Cursor = System.Windows.Forms.Cursors.Default
        Me.Panel1.Dock = System.Windows.Forms.DockStyle.Top
        Me.Panel1.Location = New System.Drawing.Point(0, 0)
        Me.Panel1.Name = "Panel1"
        Me.Panel1.Size = New System.Drawing.Size(1367, 67)
        Me.Panel1.TabIndex = 15
        '
        'btnLogin
        '
        Me.btnLogin.BackColor = System.Drawing.Color.Red
        Me.btnLogin.Cursor = System.Windows.Forms.Cursors.Hand
        Me.btnLogin.FlatStyle = System.Windows.Forms.FlatStyle.Popup
        Me.btnLogin.Font = New System.Drawing.Font("Segoe UI", 8.25!, System.Drawing.FontStyle.Bold)
        Me.btnLogin.ForeColor = System.Drawing.Color.White
        Me.btnLogin.Location = New System.Drawing.Point(1263, 12)
        Me.btnLogin.Name = "btnLogin"
        Me.btnLogin.Size = New System.Drawing.Size(78, 48)
        Me.btnLogin.TabIndex = 27
        Me.btnLogin.Text = "Log out"
        Me.btnLogin.UseVisualStyleBackColor = False
        '
        'PictureBox1
        '
        Me.PictureBox1.BackColor = System.Drawing.Color.Transparent
        Me.PictureBox1.Image = CType(resources.GetObject("PictureBox1.Image"), System.Drawing.Image)
        Me.PictureBox1.Location = New System.Drawing.Point(6, -24)
        Me.PictureBox1.Name = "PictureBox1"
        Me.PictureBox1.Size = New System.Drawing.Size(96, 113)
        Me.PictureBox1.SizeMode = System.Windows.Forms.PictureBoxSizeMode.StretchImage
        Me.PictureBox1.TabIndex = 3
        Me.PictureBox1.TabStop = False
        '
        'Label11
        '
        Me.Label11.AutoSize = True
        Me.Label11.BackColor = System.Drawing.Color.Transparent
        Me.Label11.Font = New System.Drawing.Font("Segoe UI", 15.0!, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.Label11.ForeColor = System.Drawing.Color.Black
        Me.Label11.Location = New System.Drawing.Point(108, 9)
        Me.Label11.Name = "Label11"
        Me.Label11.Size = New System.Drawing.Size(226, 35)
        Me.Label11.TabIndex = 24
        Me.Label11.Text = "Admin Dashboard"
        '
        'Label1
        '
        Me.Label1.AutoSize = True
        Me.Label1.BackColor = System.Drawing.Color.Transparent
        Me.Label1.Font = New System.Drawing.Font("Segoe UI", 15.0!, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.Label1.ForeColor = System.Drawing.Color.Black
        Me.Label1.Location = New System.Drawing.Point(-87, 32)
        Me.Label1.Name = "Label1"
        Me.Label1.Size = New System.Drawing.Size(226, 35)
        Me.Label1.TabIndex = 17
        Me.Label1.Text = "Admin Dashboard"
        '
        'Panel8
        '
        Me.Panel8.BackColor = System.Drawing.SystemColors.ActiveCaption
        Me.Panel8.Controls.Add(Me.Panel3)
        Me.Panel8.Controls.Add(Me.Panel11)
        Me.Panel8.Controls.Add(Me.Panel9)
        Me.Panel8.Controls.Add(Me.Panel12)
        Me.Panel8.Controls.Add(Me.Panel10)
        Me.Panel8.Cursor = System.Windows.Forms.Cursors.Default
        Me.Panel8.Dock = System.Windows.Forms.DockStyle.Left
        Me.Panel8.Location = New System.Drawing.Point(0, 67)
        Me.Panel8.Name = "Panel8"
        Me.Panel8.Size = New System.Drawing.Size(196, 799)
        Me.Panel8.TabIndex = 25
        '
        'Panel3
        '
        Me.Panel3.Controls.Add(Me.btntimesbooksreturned)
        Me.Panel3.Cursor = System.Windows.Forms.Cursors.Hand
        Me.Panel3.Location = New System.Drawing.Point(6, 238)
        Me.Panel3.Name = "Panel3"
        Me.Panel3.Size = New System.Drawing.Size(176, 72)
        Me.Panel3.TabIndex = 26
        '
        'btntimesbooksreturned
        '
        Me.btntimesbooksreturned.BackColor = System.Drawing.SystemColors.ActiveCaption
        Me.btntimesbooksreturned.FlatStyle = System.Windows.Forms.FlatStyle.Popup
        Me.btntimesbooksreturned.Font = New System.Drawing.Font("Segoe UI", 8.25!, System.Drawing.FontStyle.Bold)
        Me.btntimesbooksreturned.ForeColor = System.Drawing.Color.White
        Me.btntimesbooksreturned.Image = CType(resources.GetObject("btntimesbooksreturned.Image"), System.Drawing.Image)
        Me.btntimesbooksreturned.ImageAlign = System.Drawing.ContentAlignment.MiddleLeft
        Me.btntimesbooksreturned.Location = New System.Drawing.Point(-15, -5)
        Me.btntimesbooksreturned.Name = "btntimesbooksreturned"
        Me.btntimesbooksreturned.Padding = New System.Windows.Forms.Padding(25, 0, 0, 0)
        Me.btntimesbooksreturned.Size = New System.Drawing.Size(211, 110)
        Me.btntimesbooksreturned.TabIndex = 7
        Me.btntimesbooksreturned.Text = "         Time books     returned"
        Me.btntimesbooksreturned.UseVisualStyleBackColor = False
        '
        'Panel11
        '
        Me.Panel11.Controls.Add(Me.btnRegistereduser)
        Me.Panel11.Cursor = System.Windows.Forms.Cursors.Hand
        Me.Panel11.Location = New System.Drawing.Point(-2, 108)
        Me.Panel11.Name = "Panel11"
        Me.Panel11.Size = New System.Drawing.Size(205, 91)
        Me.Panel11.TabIndex = 28
        '
        'btnRegistereduser
        '
        Me.btnRegistereduser.BackColor = System.Drawing.SystemColors.ActiveCaption
        Me.btnRegistereduser.FlatStyle = System.Windows.Forms.FlatStyle.Popup
        Me.btnRegistereduser.Font = New System.Drawing.Font("Segoe UI", 8.25!, System.Drawing.FontStyle.Bold)
        Me.btnRegistereduser.ForeColor = System.Drawing.Color.White
        Me.btnRegistereduser.Image = CType(resources.GetObject("btnRegistereduser.Image"), System.Drawing.Image)
        Me.btnRegistereduser.ImageAlign = System.Drawing.ContentAlignment.MiddleLeft
        Me.btnRegistereduser.Location = New System.Drawing.Point(-8, -4)
        Me.btnRegistereduser.Name = "btnRegistereduser"
        Me.btnRegistereduser.Padding = New System.Windows.Forms.Padding(25, 0, 0, 0)
        Me.btnRegistereduser.Size = New System.Drawing.Size(222, 116)
        Me.btnRegistereduser.TabIndex = 7
        Me.btnRegistereduser.Text = "         Registered user"
        Me.btnRegistereduser.UseVisualStyleBackColor = False
        '
        'Panel9
        '
        Me.Panel9.Controls.Add(Me.btnBooklisted)
        Me.Panel9.Cursor = System.Windows.Forms.Cursors.Hand
        Me.Panel9.Location = New System.Drawing.Point(-8, 0)
        Me.Panel9.Name = "Panel9"
        Me.Panel9.Size = New System.Drawing.Size(243, 82)
        Me.Panel9.TabIndex = 27
        '
        'btnBooklisted
        '
        Me.btnBooklisted.BackColor = System.Drawing.SystemColors.ActiveCaption
        Me.btnBooklisted.FlatAppearance.BorderSize = 0
        Me.btnBooklisted.FlatStyle = System.Windows.Forms.FlatStyle.Popup
        Me.btnBooklisted.Font = New System.Drawing.Font("Segoe UI", 8.25!, System.Drawing.FontStyle.Bold)
        Me.btnBooklisted.ForeColor = System.Drawing.Color.White
        Me.btnBooklisted.Image = CType(resources.GetObject("btnBooklisted.Image"), System.Drawing.Image)
        Me.btnBooklisted.ImageAlign = System.Drawing.ContentAlignment.MiddleLeft
        Me.btnBooklisted.Location = New System.Drawing.Point(-4, -2)
        Me.btnBooklisted.Name = "btnBooklisted"
        Me.btnBooklisted.Padding = New System.Windows.Forms.Padding(25, 0, 0, 0)
        Me.btnBooklisted.Size = New System.Drawing.Size(214, 91)
        Me.btnBooklisted.TabIndex = 7
        Me.btnBooklisted.Text = "  Book list"
        Me.btnBooklisted.UseVisualStyleBackColor = False
        '
        'Panel12
        '
        Me.Panel12.Controls.Add(Me.btnReturnrequests)
        Me.Panel12.Cursor = System.Windows.Forms.Cursors.Hand
        Me.Panel12.Location = New System.Drawing.Point(1, 184)
        Me.Panel12.Name = "Panel12"
        Me.Panel12.Size = New System.Drawing.Size(197, 74)
        Me.Panel12.TabIndex = 29
        '
        'btnReturnrequests
        '
        Me.btnReturnrequests.BackColor = System.Drawing.SystemColors.ActiveCaption
        Me.btnReturnrequests.FlatStyle = System.Windows.Forms.FlatStyle.Popup
        Me.btnReturnrequests.Font = New System.Drawing.Font("Segoe UI", 8.25!, System.Drawing.FontStyle.Bold)
        Me.btnReturnrequests.ForeColor = System.Drawing.Color.White
        Me.btnReturnrequests.Image = CType(resources.GetObject("btnReturnrequests.Image"), System.Drawing.Image)
        Me.btnReturnrequests.ImageAlign = System.Drawing.ContentAlignment.MiddleLeft
        Me.btnReturnrequests.Location = New System.Drawing.Point(-10, -2)
        Me.btnReturnrequests.Name = "btnReturnrequests"
        Me.btnReturnrequests.Padding = New System.Windows.Forms.Padding(25, 0, 0, 0)
        Me.btnReturnrequests.Size = New System.Drawing.Size(218, 81)
        Me.btnReturnrequests.TabIndex = 7
        Me.btnReturnrequests.Text = "        Return requests"
        Me.btnReturnrequests.UseVisualStyleBackColor = False
        '
        'Panel10
        '
        Me.Panel10.Controls.Add(Me.btnBorrowrequests)
        Me.Panel10.Cursor = System.Windows.Forms.Cursors.Hand
        Me.Panel10.Location = New System.Drawing.Point(-15, 62)
        Me.Panel10.Name = "Panel10"
        Me.Panel10.Size = New System.Drawing.Size(229, 82)
        Me.Panel10.TabIndex = 27
        '
        'btnBorrowrequests
        '
        Me.btnBorrowrequests.BackColor = System.Drawing.SystemColors.ActiveCaption
        Me.btnBorrowrequests.FlatAppearance.BorderSize = 0
        Me.btnBorrowrequests.FlatStyle = System.Windows.Forms.FlatStyle.Popup
        Me.btnBorrowrequests.Font = New System.Drawing.Font("Segoe UI", 8.25!, System.Drawing.FontStyle.Bold)
        Me.btnBorrowrequests.ForeColor = System.Drawing.Color.White
        Me.btnBorrowrequests.Image = CType(resources.GetObject("btnBorrowrequests.Image"), System.Drawing.Image)
        Me.btnBorrowrequests.ImageAlign = System.Drawing.ContentAlignment.MiddleLeft
        Me.btnBorrowrequests.Location = New System.Drawing.Point(12, -5)
        Me.btnBorrowrequests.Name = "btnBorrowrequests"
        Me.btnBorrowrequests.Padding = New System.Windows.Forms.Padding(25, 0, 0, 0)
        Me.btnBorrowrequests.Size = New System.Drawing.Size(222, 86)
        Me.btnBorrowrequests.TabIndex = 7
        Me.btnBorrowrequests.Text = "     Borrow requests"
        Me.btnBorrowrequests.UseVisualStyleBackColor = False
        '
        'gbList
        '
        Me.gbList.Controls.Add(Me.dgvLoadbooks)
        Me.gbList.Location = New System.Drawing.Point(211, 142)
        Me.gbList.Name = "gbList"
        Me.gbList.Size = New System.Drawing.Size(1141, 654)
        Me.gbList.TabIndex = 26
        Me.gbList.TabStop = False
        '
        'dgvLoadbooks
        '
        Me.dgvLoadbooks.AutoSizeColumnsMode = System.Windows.Forms.DataGridViewAutoSizeColumnsMode.Fill
        Me.dgvLoadbooks.ColumnHeadersHeightSizeMode = System.Windows.Forms.DataGridViewColumnHeadersHeightSizeMode.AutoSize
        Me.dgvLoadbooks.Cursor = System.Windows.Forms.Cursors.Hand
        Me.dgvLoadbooks.Location = New System.Drawing.Point(5, 21)
        Me.dgvLoadbooks.Name = "dgvLoadbooks"
        Me.dgvLoadbooks.RowHeadersVisible = False
        Me.dgvLoadbooks.RowHeadersWidth = 51
        Me.dgvLoadbooks.RowTemplate.Height = 24
        Me.dgvLoadbooks.Size = New System.Drawing.Size(1124, 616)
        Me.dgvLoadbooks.TabIndex = 0
        '
        'txtSearch
        '
        Me.txtSearch.BorderStyle = System.Windows.Forms.BorderStyle.None
        Me.txtSearch.Font = New System.Drawing.Font("Segoe UI", 10.0!)
        Me.txtSearch.ForeColor = System.Drawing.Color.Black
        Me.txtSearch.Location = New System.Drawing.Point(917, 98)
        Me.txtSearch.Multiline = True
        Me.txtSearch.Name = "txtSearch"
        Me.txtSearch.Size = New System.Drawing.Size(238, 26)
        Me.txtSearch.TabIndex = 4
        '
        'Label2
        '
        Me.Label2.AutoSize = True
        Me.Label2.BackColor = System.Drawing.Color.Transparent
        Me.Label2.Font = New System.Drawing.Font("Segoe UI", 15.0!, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.Label2.ForeColor = System.Drawing.Color.Black
        Me.Label2.Location = New System.Drawing.Point(217, 70)
        Me.Label2.Name = "Label2"
        Me.Label2.Size = New System.Drawing.Size(99, 35)
        Me.Label2.TabIndex = 28
        Me.Label2.Text = "HELLO!"
        '
        'Label3
        '
        Me.Label3.AutoSize = True
        Me.Label3.BackColor = System.Drawing.Color.Transparent
        Me.Label3.Font = New System.Drawing.Font("Segoe UI", 13.0!, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.Label3.ForeColor = System.Drawing.Color.Black
        Me.Label3.Location = New System.Drawing.Point(222, 105)
        Me.Label3.Name = "Label3"
        Me.Label3.Size = New System.Drawing.Size(81, 30)
        Me.Label3.TabIndex = 24
        Me.Label3.Text = "Admin"
        '
        'btnAddbooks
        '
        Me.btnAddbooks.BackColor = System.Drawing.Color.SkyBlue
        Me.btnAddbooks.Cursor = System.Windows.Forms.Cursors.Hand
        Me.btnAddbooks.FlatStyle = System.Windows.Forms.FlatStyle.Popup
        Me.btnAddbooks.Font = New System.Drawing.Font("Segoe UI", 8.25!, System.Drawing.FontStyle.Bold)
        Me.btnAddbooks.ForeColor = System.Drawing.Color.White
        Me.btnAddbooks.Image = CType(resources.GetObject("btnAddbooks.Image"), System.Drawing.Image)
        Me.btnAddbooks.ImageAlign = System.Drawing.ContentAlignment.MiddleLeft
        Me.btnAddbooks.Location = New System.Drawing.Point(1173, 84)
        Me.btnAddbooks.Name = "btnAddbooks"
        Me.btnAddbooks.Padding = New System.Windows.Forms.Padding(25, 0, 0, 0)
        Me.btnAddbooks.Size = New System.Drawing.Size(179, 52)
        Me.btnAddbooks.TabIndex = 1
        Me.btnAddbooks.Text = "ADD" & Global.Microsoft.VisualBasic.ChrW(13) & Global.Microsoft.VisualBasic.ChrW(10) & "BOOKS"
        Me.btnAddbooks.UseVisualStyleBackColor = False
        '
        'btnAdduser
        '
        Me.btnAdduser.Cursor = System.Windows.Forms.Cursors.Hand
        Me.btnAdduser.Location = New System.Drawing.Point(1230, 84)
        Me.btnAdduser.Name = "btnAdduser"
        Me.btnAdduser.Size = New System.Drawing.Size(111, 52)
        Me.btnAdduser.TabIndex = 1
        Me.btnAdduser.Text = "Add user"
        Me.btnAdduser.UseVisualStyleBackColor = True
        '
        'frmAdmindashboard
        '
        Me.AutoScaleDimensions = New System.Drawing.SizeF(8.0!, 16.0!)
        Me.AutoScaleMode = System.Windows.Forms.AutoScaleMode.Font
        Me.ClientSize = New System.Drawing.Size(1367, 866)
        Me.Controls.Add(Me.btnAdduser)
        Me.Controls.Add(Me.Label2)
        Me.Controls.Add(Me.txtSearch)
        Me.Controls.Add(Me.Label3)
        Me.Controls.Add(Me.gbList)
        Me.Controls.Add(Me.Panel8)
        Me.Controls.Add(Me.Panel1)
        Me.Controls.Add(Me.btnAddbooks)
        Me.Controls.Add(Me.Label1)
        Me.Cursor = System.Windows.Forms.Cursors.Default
        Me.Name = "frmAdmindashboard"
        Me.StartPosition = System.Windows.Forms.FormStartPosition.CenterScreen
        Me.Text = "Admin Dashboard"
        Me.Panel1.ResumeLayout(False)
        Me.Panel1.PerformLayout()
        CType(Me.PictureBox1, System.ComponentModel.ISupportInitialize).EndInit()
        Me.Panel8.ResumeLayout(False)
        Me.Panel3.ResumeLayout(False)
        Me.Panel11.ResumeLayout(False)
        Me.Panel9.ResumeLayout(False)
        Me.Panel12.ResumeLayout(False)
        Me.Panel10.ResumeLayout(False)
        Me.gbList.ResumeLayout(False)
        CType(Me.dgvLoadbooks, System.ComponentModel.ISupportInitialize).EndInit()
        Me.ResumeLayout(False)
        Me.PerformLayout()

    End Sub
    Friend WithEvents Label4 As Label
    Friend WithEvents PictureBox1 As PictureBox
    Friend WithEvents Panel1 As Panel
    Friend WithEvents Label1 As Label
    Friend WithEvents Label11 As Label
    Friend WithEvents Panel8 As Panel
    Friend WithEvents btnAddbooks As Button
    Friend WithEvents btnReturnrequests As Button
    Friend WithEvents Panel3 As Panel
    Friend WithEvents Panel9 As Panel
    Friend WithEvents btnBooklisted As Button
    Friend WithEvents Panel10 As Panel
    Friend WithEvents btnBorrowrequests As Button
    Friend WithEvents Panel11 As Panel
    Friend WithEvents btnRegistereduser As Button
    Friend WithEvents btntimesbooksreturned As Button
    Friend WithEvents Panel12 As Panel
    Friend WithEvents gbList As GroupBox
    Friend WithEvents dgvLoadbooks As DataGridView
    Friend WithEvents btnLogin As Button
    Friend WithEvents txtSearch As TextBox
    Friend WithEvents Label2 As Label
    Friend WithEvents Label3 As Label
    Friend WithEvents btnAdduser As Button
End Class
