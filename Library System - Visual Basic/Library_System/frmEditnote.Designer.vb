<Global.Microsoft.VisualBasic.CompilerServices.DesignerGenerated()>
Partial Class frmEditNote
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
        Me.txtNote = New System.Windows.Forms.TextBox()
        Me.btnSavenote = New System.Windows.Forms.Button()
        Me.btnCloseNote = New System.Windows.Forms.Button()
        Me.lblBorrower = New System.Windows.Forms.Label()
        Me.SuspendLayout()
        '
        'txtNote
        '
        Me.txtNote.Location = New System.Drawing.Point(12, 60)
        Me.txtNote.Multiline = True
        Me.txtNote.Name = "txtNote"
        Me.txtNote.Size = New System.Drawing.Size(463, 211)
        Me.txtNote.TabIndex = 0
        '
        'btnSavenote
        '
        Me.btnSavenote.Location = New System.Drawing.Point(270, 286)
        Me.btnSavenote.Name = "btnSavenote"
        Me.btnSavenote.Size = New System.Drawing.Size(89, 35)
        Me.btnSavenote.TabIndex = 1
        Me.btnSavenote.Text = "Save note"
        Me.btnSavenote.UseVisualStyleBackColor = True
        '
        'btnCloseNote
        '
        Me.btnCloseNote.Location = New System.Drawing.Point(365, 286)
        Me.btnCloseNote.Name = "btnCloseNote"
        Me.btnCloseNote.Size = New System.Drawing.Size(89, 35)
        Me.btnCloseNote.TabIndex = 1
        Me.btnCloseNote.Text = "Close"
        Me.btnCloseNote.UseVisualStyleBackColor = True
        '
        'lblBorrower
        '
        Me.lblBorrower.AutoSize = True
        Me.lblBorrower.BackColor = System.Drawing.Color.Transparent
        Me.lblBorrower.Font = New System.Drawing.Font("Felix Titling", 7.8!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.lblBorrower.Location = New System.Drawing.Point(12, 22)
        Me.lblBorrower.Name = "lblBorrower"
        Me.lblBorrower.Size = New System.Drawing.Size(93, 16)
        Me.lblBorrower.TabIndex = 2
        Me.lblBorrower.Text = "Borrower"
        '
        'frmEditNote
        '
        Me.AutoScaleDimensions = New System.Drawing.SizeF(8.0!, 16.0!)
        Me.AutoScaleMode = System.Windows.Forms.AutoScaleMode.Font
        Me.ClientSize = New System.Drawing.Size(487, 333)
        Me.Controls.Add(Me.lblBorrower)
        Me.Controls.Add(Me.btnCloseNote)
        Me.Controls.Add(Me.btnSavenote)
        Me.Controls.Add(Me.txtNote)
        Me.Name = "frmEditNote"
        Me.StartPosition = System.Windows.Forms.FormStartPosition.CenterScreen
        Me.Text = "Note"
        Me.ResumeLayout(False)
        Me.PerformLayout()

    End Sub

    Friend WithEvents txtNote As TextBox
    Friend WithEvents btnSavenote As Button
    Friend WithEvents btnCloseNote As Button
    Friend WithEvents lblBorrower As Label
End Class
