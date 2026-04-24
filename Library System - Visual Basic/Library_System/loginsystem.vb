Public Class login
    Private Sub login_Load(sender As Object, e As EventArgs) Handles MyBase.Load
        txtEmail.Text = "Email"
        txtEmail.ForeColor = Color.DarkGray

        txtPassword.Text = "Password"
        txtPassword.ForeColor = Color.DarkGray
    End Sub

    Private Sub txtEmail_TextChanged(sender As Object, e As EventArgs) Handles txtEmail.TextChanged

    End Sub

    Private Sub txtEmail_GotFocus(sender As Object, e As EventArgs) Handles txtEmail.GotFocus
        If txtEmail.Text = "Email" Then
            txtEmail.Text = ""
            txtEmail.ForeColor = Color.Black
        End If
    End Sub

    Private Sub txtUsername_LostFocus(sender As Object, e As EventArgs) Handles txtEmail.LostFocus
        If txtEmail.Text = "" Then
            txtEmail.Text = "Email"
            txtEmail.ForeColor = Color.DarkGray
        End If
    End Sub

    Private Sub txtPassword_GotFocus(sender As Object, e As EventArgs) Handles txtPassword.GotFocus
        If txtPassword.Text = "Password" Then
            txtPassword.Text = ""
            txtPassword.PasswordChar = "●"
            txtPassword.ForeColor = Color.Black
        End If
    End Sub

    Private Sub txtPassword_LostFocus(sender As Object, e As EventArgs) Handles txtPassword.LostFocus
        If txtPassword.Text = "" Then
            txtPassword.Text = "Password"
            txtPassword.PasswordChar = ""
            txtPassword.ForeColor = Color.DarkGray
        End If
    End Sub


    Private Sub Form1_Load(sender As Object, e As EventArgs) Handles MyBase.Load
        txtPassword.PasswordChar = "●"c
        pbShowpassword.Visible = False
        pbHidepassword.Visible = False
    End Sub

    Private Sub pbShowpassword_Click(sender As Object, e As EventArgs) Handles pbShowpassword.Click
            txtPassword.PasswordChar = ControlChars.NullChar

            pbShowpassword.Visible = False
            pbHidepassword.Visible = True
        End Sub

        Private Sub pbHidepassword_Click(sender As Object, e As EventArgs) Handles pbHidepassword.Click
            txtPassword.PasswordChar = "●"c

            pbHidepassword.Visible = False
            pbShowpassword.Visible = True
        End Sub

    Private Sub txtPassword_TextChanged(sender As Object, e As EventArgs) Handles txtPassword.TextChanged
            UpdateEyeVisibility()
        End Sub

        Private Sub txtPassword_Enter(sender As Object, e As EventArgs) Handles txtPassword.Enter
            UpdateEyeVisibility()
        End Sub

        Private Sub txtPassword_Leave(sender As Object, e As EventArgs) Handles txtPassword.Leave
            pbShowpassword.Visible = False
            pbHidepassword.Visible = False
        End Sub

    Private Sub UpdateEyeVisibility()
            If txtPassword.Focused AndAlso txtPassword.Text.Length > 0 Then

                ' Show correct icon based on current state
                If txtPassword.PasswordChar = ControlChars.NullChar Then
                    pbHidepassword.Visible = True
                    pbShowpassword.Visible = False
                Else
                    pbShowpassword.Visible = True
                    pbHidepassword.Visible = False
                End If

            Else
                pbShowpassword.Visible = False
                pbHidepassword.Visible = False
            End If
        End Sub

    Private Sub Label1_Click(sender As Object, e As EventArgs) Handles Label1.Click

    End Sub
End Class