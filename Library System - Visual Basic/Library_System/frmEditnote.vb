Imports MySql.Data.MySqlClient

Public Class frmEditNote

    Public Property RequestId As Integer
    Public Property BorrowerName As String
    Public Property ExistingNote As String

    Private connStr As String = "server=localhost;userid=root;password=;database=Library_db"

    Private Sub frmEditNote_Load(sender As Object, e As EventArgs) Handles MyBase.Load
        lblBorrower.Text = $"Note for: {BorrowerName}"
        txtNote.Text = If(String.IsNullOrEmpty(ExistingNote), "", ExistingNote)
        txtNote.Focus()
        txtNote.SelectionStart = txtNote.Text.Length
    End Sub

    Private Sub btnSavenote_Click(sender As Object, e As EventArgs) Handles btnSavenote.Click
        If txtNote.Text.Trim() = "" Then
            MessageBox.Show("Please type a note before saving.", "Empty Note",
                            MessageBoxButtons.OK, MessageBoxIcon.Warning)
            txtNote.Focus()
            Return
        End If

        Try
            Using conn As New MySqlConnection(connStr)
                conn.Open()
                Dim query As String = "UPDATE borrow_requests SET admin_note = @note WHERE id = @id"
                Using cmd As New MySqlCommand(query, conn)
                    cmd.Parameters.AddWithValue("@note", txtNote.Text.Trim())
                    cmd.Parameters.AddWithValue("@id", RequestId)
                    cmd.ExecuteNonQuery()
                End Using
            End Using

            MessageBox.Show("Note saved successfully!", "Saved ✓",
                            MessageBoxButtons.OK, MessageBoxIcon.Information)
            Me.Close()

        Catch ex As MySqlException
            MessageBox.Show("Database error: " & ex.Message, "Error",
                            MessageBoxButtons.OK, MessageBoxIcon.Error)
        Catch ex As Exception
            MessageBox.Show("Error: " & ex.Message, "Error",
                            MessageBoxButtons.OK, MessageBoxIcon.Error)
        End Try
    End Sub

    Private Sub btnCloseNote_Click(sender As Object, e As EventArgs) Handles btnCloseNote.Click
        Me.Close()
    End Sub

    Private Sub txtNote_KeyDown(sender As Object, e As KeyEventArgs) Handles txtNote.KeyDown
        If e.KeyCode = Keys.Enter Then
            btnSavenote.PerformClick()
            e.SuppressKeyPress = True
        End If
    End Sub
End Class