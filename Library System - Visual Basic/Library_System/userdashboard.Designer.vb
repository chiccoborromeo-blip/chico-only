<Global.Microsoft.VisualBasic.CompilerServices.DesignerGenerated()>
Partial Class userdashboard
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
        Dim resources As System.ComponentModel.ComponentResourceManager = New System.ComponentModel.ComponentResourceManager(GetType(userdashboard))
        Me.gbList = New System.Windows.Forms.GroupBox()
        Me.dgvLoadbooks = New System.Windows.Forms.DataGridView()
        Me.Panel8 = New System.Windows.Forms.Panel()
        Me.Panel2 = New System.Windows.Forms.Panel()
        Me.Panel9 = New System.Windows.Forms.Panel()
        Me.Label11 = New System.Windows.Forms.Label()
        Me.Label2 = New System.Windows.Forms.Label()
        Me.Label3 = New System.Windows.Forms.Label()
        Me.btnLogin = New System.Windows.Forms.Button()
        Me.Panel1 = New System.Windows.Forms.Panel()
        Me.Label1 = New System.Windows.Forms.Label()
        Me.Label4 = New System.Windows.Forms.Label()
        Me.btnMyborrowedbooks = New System.Windows.Forms.Button()
        Me.btnBooks = New System.Windows.Forms.Button()
        Me.PictureBox1 = New System.Windows.Forms.PictureBox()
        Me.gbList.SuspendLayout()
        CType(Me.dgvLoadbooks, System.ComponentModel.ISupportInitialize).BeginInit()
        Me.Panel8.SuspendLayout()
        Me.Panel2.SuspendLayout()
        Me.Panel9.SuspendLayout()
        Me.Panel1.SuspendLayout()
        CType(Me.PictureBox1, System.ComponentModel.ISupportInitialize).BeginInit()
        Me.SuspendLayout()
        '
        'gbList
        '
        Me.gbList.Controls.Add(Me.dgvLoadbooks)
        Me.gbList.Location = New System.Drawing.Point(202, 150)
        Me.gbList.Name = "gbList"
        Me.gbList.Size = New System.Drawing.Size(1141, 614)
        Me.gbList.TabIndex = 33
        Me.gbList.TabStop = False
        Me.gbList.Text = "Book list"
        '
        'dgvLoadbooks
        '
        Me.dgvLoadbooks.AutoSizeColumnsMode = System.Windows.Forms.DataGridViewAutoSizeColumnsMode.Fill
        Me.dgvLoadbooks.ColumnHeadersHeightSizeMode = System.Windows.Forms.DataGridViewColumnHeadersHeightSizeMode.AutoSize
        Me.dgvLoadbooks.Location = New System.Drawing.Point(6, 21)
        Me.dgvLoadbooks.Name = "dgvLoadbooks"
        Me.dgvLoadbooks.RowHeadersBorderStyle = System.Windows.Forms.DataGridViewHeaderBorderStyle.None
        Me.dgvLoadbooks.RowHeadersWidth = 51
        Me.dgvLoadbooks.RowTemplate.Height = 24
        Me.dgvLoadbooks.Size = New System.Drawing.Size(1124, 584)
        Me.dgvLoadbooks.TabIndex = 0
        '
        'Panel8
        '
        Me.Panel8.BackColor = System.Drawing.SystemColors.ActiveCaption
        Me.Panel8.Controls.Add(Me.Panel2)
        Me.Panel8.Controls.Add(Me.Panel9)
        Me.Panel8.Cursor = System.Windows.Forms.Cursors.Default
        Me.Panel8.Dock = System.Windows.Forms.DockStyle.Left
        Me.Panel8.Location = New System.Drawing.Point(0, 97)
        Me.Panel8.Name = "Panel8"
        Me.Panel8.Size = New System.Drawing.Size(196, 689)
        Me.Panel8.TabIndex = 32
        '
        'Panel2
        '
        Me.Panel2.Controls.Add(Me.btnMyborrowedbooks)
        Me.Panel2.Cursor = System.Windows.Forms.Cursors.Hand
        Me.Panel2.Location = New System.Drawing.Point(1, 71)
        Me.Panel2.Name = "Panel2"
        Me.Panel2.Size = New System.Drawing.Size(243, 82)
        Me.Panel2.TabIndex = 28
        '
        'Panel9
        '
        Me.Panel9.Controls.Add(Me.btnBooks)
        Me.Panel9.Cursor = System.Windows.Forms.Cursors.Hand
        Me.Panel9.Location = New System.Drawing.Point(-8, -1)
        Me.Panel9.Name = "Panel9"
        Me.Panel9.Size = New System.Drawing.Size(243, 82)
        Me.Panel9.TabIndex = 27
        '
        'Label11
        '
        Me.Label11.AutoSize = True
        Me.Label11.BackColor = System.Drawing.Color.Transparent
        Me.Label11.Font = New System.Drawing.Font("Segoe UI", 15.0!, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.Label11.ForeColor = System.Drawing.Color.Black
        Me.Label11.Location = New System.Drawing.Point(204, 97)
        Me.Label11.Name = "Label11"
        Me.Label11.Size = New System.Drawing.Size(201, 35)
        Me.Label11.TabIndex = 31
        Me.Label11.Text = "User Dashboard"
        '
        'Label2
        '
        Me.Label2.AutoSize = True
        Me.Label2.BackColor = System.Drawing.Color.Transparent
        Me.Label2.Font = New System.Drawing.Font("Segoe UI", 18.0!, System.Drawing.FontStyle.Bold)
        Me.Label2.ForeColor = System.Drawing.Color.Black
        Me.Label2.Location = New System.Drawing.Point(172, 3)
        Me.Label2.Name = "Label2"
        Me.Label2.Size = New System.Drawing.Size(118, 41)
        Me.Label2.TabIndex = 25
        Me.Label2.Text = "Library"
        '
        'Label3
        '
        Me.Label3.AutoSize = True
        Me.Label3.BackColor = System.Drawing.Color.Transparent
        Me.Label3.Font = New System.Drawing.Font("Segoe UI", 15.0!, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.Label3.ForeColor = System.Drawing.Color.Black
        Me.Label3.Location = New System.Drawing.Point(172, 44)
        Me.Label3.Name = "Label3"
        Me.Label3.Size = New System.Drawing.Size(99, 35)
        Me.Label3.TabIndex = 26
        Me.Label3.Text = "System"
        '
        'btnLogin
        '
        Me.btnLogin.BackColor = System.Drawing.Color.Red
        Me.btnLogin.Cursor = System.Windows.Forms.Cursors.Hand
        Me.btnLogin.FlatStyle = System.Windows.Forms.FlatStyle.Popup
        Me.btnLogin.Font = New System.Drawing.Font("Segoe UI", 8.25!, System.Drawing.FontStyle.Bold)
        Me.btnLogin.ForeColor = System.Drawing.Color.White
        Me.btnLogin.Location = New System.Drawing.Point(1252, 19)
        Me.btnLogin.Name = "btnLogin"
        Me.btnLogin.Size = New System.Drawing.Size(89, 48)
        Me.btnLogin.TabIndex = 27
        Me.btnLogin.Text = "Log out"
        Me.btnLogin.UseVisualStyleBackColor = False
        '
        'Panel1
        '
        Me.Panel1.BackColor = System.Drawing.Color.White
        Me.Panel1.Controls.Add(Me.btnLogin)
        Me.Panel1.Controls.Add(Me.Label3)
        Me.Panel1.Controls.Add(Me.Label2)
        Me.Panel1.Controls.Add(Me.PictureBox1)
        Me.Panel1.Cursor = System.Windows.Forms.Cursors.Default
        Me.Panel1.Dock = System.Windows.Forms.DockStyle.Top
        Me.Panel1.Location = New System.Drawing.Point(0, 0)
        Me.Panel1.Name = "Panel1"
        Me.Panel1.Size = New System.Drawing.Size(1367, 97)
        Me.Panel1.TabIndex = 28
        '
        'Label1
        '
        Me.Label1.AutoSize = True
        Me.Label1.BackColor = System.Drawing.Color.Transparent
        Me.Label1.Font = New System.Drawing.Font("Segoe UI", 15.0!, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.Label1.ForeColor = System.Drawing.Color.Black
        Me.Label1.Location = New System.Drawing.Point(-44, 32)
        Me.Label1.Name = "Label1"
        Me.Label1.Size = New System.Drawing.Size(226, 35)
        Me.Label1.TabIndex = 30
        Me.Label1.Text = "Admin Dashboard"
        '
        'Label4
        '
        Me.Label4.AutoSize = True
        Me.Label4.BackColor = System.Drawing.Color.Transparent
        Me.Label4.Font = New System.Drawing.Font("Segoe UI", 8.0!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.Label4.ForeColor = System.Drawing.Color.Black
        Me.Label4.Location = New System.Drawing.Point(208, 128)
        Me.Label4.Name = "Label4"
        Me.Label4.Size = New System.Drawing.Size(109, 19)
        Me.Label4.TabIndex = 29
        Me.Label4.Text = "Library overview"
        '
        'btnMyborrowedbooks
        '
        Me.btnMyborrowedbooks.BackColor = System.Drawing.SystemColors.ActiveCaption
        Me.btnMyborrowedbooks.FlatAppearance.BorderSize = 0
        Me.btnMyborrowedbooks.FlatStyle = System.Windows.Forms.FlatStyle.Popup
        Me.btnMyborrowedbooks.Font = New System.Drawing.Font("Segoe UI", 8.25!, System.Drawing.FontStyle.Bold)
        Me.btnMyborrowedbooks.ForeColor = System.Drawing.Color.White
        Me.btnMyborrowedbooks.Image = CType(resources.GetObject("btnMyborrowedbooks.Image"), System.Drawing.Image)
        Me.btnMyborrowedbooks.ImageAlign = System.Drawing.ContentAlignment.MiddleLeft
        Me.btnMyborrowedbooks.Location = New System.Drawing.Point(-13, -2)
        Me.btnMyborrowedbooks.Name = "btnMyborrowedbooks"
        Me.btnMyborrowedbooks.Padding = New System.Windows.Forms.Padding(25, 0, 0, 0)
        Me.btnMyborrowedbooks.Size = New System.Drawing.Size(214, 91)
        Me.btnMyborrowedbooks.TabIndex = 7
        Me.btnMyborrowedbooks.Text = "      My borrowed " & Global.Microsoft.VisualBasic.ChrW(13) & Global.Microsoft.VisualBasic.ChrW(10) & "  books"
        Me.btnMyborrowedbooks.UseVisualStyleBackColor = False
        '
        'btnBooks
        '
        Me.btnBooks.BackColor = System.Drawing.SystemColors.ActiveCaption
        Me.btnBooks.FlatAppearance.BorderSize = 0
        Me.btnBooks.FlatStyle = System.Windows.Forms.FlatStyle.Popup
        Me.btnBooks.Font = New System.Drawing.Font("Segoe UI", 8.25!, System.Drawing.FontStyle.Bold)
        Me.btnBooks.ForeColor = System.Drawing.Color.White
        Me.btnBooks.Image = CType(resources.GetObject("btnBooks.Image"), System.Drawing.Image)
        Me.btnBooks.ImageAlign = System.Drawing.ContentAlignment.MiddleLeft
        Me.btnBooks.Location = New System.Drawing.Point(-3, -2)
        Me.btnBooks.Name = "btnBooks"
        Me.btnBooks.Padding = New System.Windows.Forms.Padding(25, 0, 0, 0)
        Me.btnBooks.Size = New System.Drawing.Size(214, 91)
        Me.btnBooks.TabIndex = 7
        Me.btnBooks.Text = "Books"
        Me.btnBooks.UseVisualStyleBackColor = False
        '
        'PictureBox1
        '
        Me.PictureBox1.BackColor = System.Drawing.Color.Transparent
        Me.PictureBox1.Image = CType(resources.GetObject("PictureBox1.Image"), System.Drawing.Image)
        Me.PictureBox1.Location = New System.Drawing.Point(3, -50)
        Me.PictureBox1.Name = "PictureBox1"
        Me.PictureBox1.Size = New System.Drawing.Size(163, 184)
        Me.PictureBox1.SizeMode = System.Windows.Forms.PictureBoxSizeMode.StretchImage
        Me.PictureBox1.TabIndex = 3
        Me.PictureBox1.TabStop = False
        '
        'userdashboard
        '
        Me.AutoScaleDimensions = New System.Drawing.SizeF(8.0!, 16.0!)
        Me.AutoScaleMode = System.Windows.Forms.AutoScaleMode.Font
        Me.ClientSize = New System.Drawing.Size(1367, 786)
        Me.Controls.Add(Me.gbList)
        Me.Controls.Add(Me.Panel8)
        Me.Controls.Add(Me.Label11)
        Me.Controls.Add(Me.Panel1)
        Me.Controls.Add(Me.Label1)
        Me.Controls.Add(Me.Label4)
        Me.Name = "userdashboard"
        Me.Text = "Form1"
        Me.gbList.ResumeLayout(False)
        CType(Me.dgvLoadbooks, System.ComponentModel.ISupportInitialize).EndInit()
        Me.Panel8.ResumeLayout(False)
        Me.Panel2.ResumeLayout(False)
        Me.Panel9.ResumeLayout(False)
        Me.Panel1.ResumeLayout(False)
        Me.Panel1.PerformLayout()
        CType(Me.PictureBox1, System.ComponentModel.ISupportInitialize).EndInit()
        Me.ResumeLayout(False)
        Me.PerformLayout()

    End Sub
    Friend WithEvents btnBooks As Button
    Friend WithEvents gbList As GroupBox
    Friend WithEvents dgvLoadbooks As DataGridView
    Friend WithEvents Panel8 As Panel
    Friend WithEvents Panel9 As Panel
    Friend WithEvents Label11 As Label
    Friend WithEvents Label2 As Label
    Friend WithEvents PictureBox1 As PictureBox
    Friend WithEvents Label3 As Label
    Friend WithEvents btnLogin As Button
    Friend WithEvents Panel1 As Panel
    Friend WithEvents Label1 As Label
    Friend WithEvents Label4 As Label
    Friend WithEvents Panel2 As Panel
    Friend WithEvents btnMyborrowedbooks As Button
End Class
